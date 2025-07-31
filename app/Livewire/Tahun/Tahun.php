<?php

namespace App\Livewire\Tahun;

use App\Models\Tahun as TahunModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Tahun extends Component
{
    use WithPagination;

    public $showModal = false;
    public $showDeleteModal = false;
    public $isEditMode = false;
    public $tahunId = null;
    public $tahunToDelete = null;
    public $modalMessage = null;
    public $modalMessageType = null;
    public $search = ''; // Added for table search
    public $form = [
        'tahun' => '',
        'status' => '', // Changed to string for dropdown
    ];

    public function mount()
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }
        $this->resetForm();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->resetErrorBag();
        $this->form = [
            'tahun' => '',
            'status' => '', // Initialize as empty string
        ];
        $this->isEditMode = false;
        $this->tahunId = null;
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->tahunToDelete = null;
        $this->modalMessage = null;
        $this->modalMessageType = null;
        $this->search = '';
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->dispatch('show-modal');
    }

    public function edit($id)
    {
        $tahun = TahunModel::findOrFail($id);
        $this->form = [
            'tahun' => $tahun->tahun,
            'status' => $tahun->status, // Use string value directly
        ];
        $this->tahunId = $id;
        $this->isEditMode = true;
        $this->showModal = true;
        $this->modalMessage = null;
        $this->modalMessageType = null;
        $this->dispatch('show-modal');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('hide-modal');
    }

    public function confirmDelete($id)
    {
        $this->tahunToDelete = $id;
        $this->showDeleteModal = true;
        $this->dispatch('show-delete-modal');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->tahunToDelete = null;
        $this->dispatch('hide-delete-modal');
    }

    public function save()
    {
        // Clear previous modal messages
        $this->modalMessage = null;
        $this->modalMessageType = null;

        // Clean and normalize the form data
        $this->form['tahun'] = trim($this->form['tahun']);

        // Validate with custom messages
        $validated = $this->validate([
            'form.tahun' => ['required', 'integer', 'min:1900', 'max:2100', 'unique:tahun,tahun,' . ($this->tahunId ?: 'NULL')],
            'form.status' => ['required', 'in:aktif,nonaktif'],
        ], [
            'form.tahun.required' => 'Tahun harus diisi.',
            'form.tahun.integer' => 'Tahun harus berupa angka.',
            'form.tahun.min' => 'Tahun minimal 1900.',
            'form.tahun.max' => 'Tahun maksimal 2100.',
            'form.tahun.unique' => 'Tahun sudah ada.',
            'form.status.required' => 'Status harus dipilih.',
            'form.status.in' => 'Status tidak valid.',
        ]);

        try {
            // If setting status to 'aktif', ensure no other year is active
            if ($validated['form']['status'] === 'aktif') {
                $activeYearExists = TahunModel::where('status', 'aktif')
                    ->when($this->tahunId, function ($query) {
                        return $query->where('id', '!=', $this->tahunId);
                    })
                    ->exists();

                if ($activeYearExists) {
                    $this->modalMessage = 'Hanya satu tahun yang dapat aktif. Nonaktifkan tahun aktif lainnya terlebih dahulu.';
                    $this->modalMessageType = 'error';
                    return;
                }
            }

            if ($this->isEditMode) {
                $tahun = TahunModel::findOrFail($this->tahunId);
                $tahun->update([
                    'tahun' => $validated['form']['tahun'],
                    'status' => $validated['form']['status'],
                ]);
                session()->flash('success', 'Tahun berhasil diperbarui!');
            } else {
                TahunModel::create([
                    'tahun' => $validated['form']['tahun'],
                    'status' => $validated['form']['status'],
                ]);
                session()->flash('success', 'Tahun berhasil ditambahkan!');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            $this->modalMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            $this->modalMessageType = 'error';
        }
    }

    public function delete()
    {
        try {
            $tahun = TahunModel::findOrFail($this->tahunToDelete);

            // Check if the year being deleted is currently active
            if ($tahun->status === 'aktif') {
                session()->flash('error', 'Tidak dapat menghapus tahun yang sedang aktif. Nonaktifkan terlebih dahulu.');
                $this->closeDeleteModal();
                return;
            }

            $tahun->delete();
            session()->flash('success', 'Tahun berhasil dihapus!');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus tahun: ' . $e->getMessage());
            $this->closeDeleteModal();
        }
    }

    public function render()
    {
        $tahuns = TahunModel::when($this->search, function ($query) {
            $query->where('tahun', 'like', '%' . $this->search . '%')
                  ->orWhere('status', 'like', '%' . $this->search . '%');
        })
        ->orderBy('tahun', 'desc')
        ->paginate(10);

        return view('livewire.tahun.index', [
            'tahuns' => $tahuns,
            'isEditMode' => $this->isEditMode,
            'showModal' => $this->showModal,
            'showDeleteModal' => $this->showDeleteModal,
            'form' => $this->form,
            'modalMessage' => $this->modalMessage,
            'modalMessageType' => $this->modalMessageType,
        ])->layout('components.layouts.app', ['title' => __('Manajemen Tahun')]);
    }
}