<?php

namespace App\Livewire\Jabatan;

use App\Models\Jabatan as JabatanModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Jabatan extends Component
{
    use WithPagination;

    public $showModal = false;
    public $showDeleteModal = false;
    public $isEditMode = false;
    public $jabatanId = null;
    public $jabatanToDelete = null;
    public $search = '';
    public $modalMessage = '';
    public $modalMessageType = 'success';
    public $form = [
        'jabatan' => '',
        'unit' => '',
    ];

    protected $messages = [
        'form.jabatan.required' => 'Nama Jabatan harus diisi.',
        'form.jabatan.string' => 'Nama Jabatan harus berupa teks.',
        'form.jabatan.max' => 'Nama Jabatan maksimal 255 karakter.',
        'form.unit.required' => 'Unit harus diisi.',
        'form.unit.string' => 'Unit harus berupa teks.',
        'form.unit.max' => 'Unit maksimal 255 karakter.',
    ];

    public function mount()
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->resetErrorBag();
        $this->form = [
            'jabatan' => '',
            'unit' => '',
        ];
        $this->isEditMode = false;
        $this->jabatanId = null;
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->jabatanToDelete = null;
        $this->modalMessage = '';
        $this->modalMessageType = 'success';
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->dispatch('show-modal');
    }

    public function edit($id)
    {
        $jabatan = JabatanModel::findOrFail($id);
        $this->form = [
            'jabatan' => $jabatan->jabatan,
            'unit' => $jabatan->unit,
        ];
        $this->jabatanId = $id;
        $this->isEditMode = true;
        $this->showModal = true;
        $this->modalMessage = '';
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
        $this->jabatanToDelete = $id;
        $this->showDeleteModal = true;
        $this->dispatch('show-delete-modal');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->jabatanToDelete = null;
        $this->dispatch('hide-delete-modal');
    }

    public function save()
    {
        $validated = $this->validate([
            'form.jabatan' => ['required', 'string', 'max:255'],
            'form.unit' => ['required', 'string', 'max:255'],
        ]);

        try {
            if ($this->isEditMode) {
                $jabatan = JabatanModel::findOrFail($this->jabatanId);
                $jabatan->update([
                    'jabatan' => $validated['form']['jabatan'],
                    'unit' => $validated['form']['unit'],
                ]);
                $this->modalMessage = 'Jabatan berhasil diperbarui!';
                $this->modalMessageType = 'success';
                session()->flash('success', 'Jabatan berhasil diperbarui!');
            } else {
                JabatanModel::create([
                    'jabatan' => $validated['form']['jabatan'],
                    'unit' => $validated['form']['unit'],
                ]);
                $this->modalMessage = 'Jabatan berhasil ditambahkan!';
                $this->modalMessageType = 'success';
                session()->flash('success', 'Jabatan berhasil ditambahkan!');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            $this->modalMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            $this->modalMessageType = 'error';
            $this->dispatch('show-modal');
        }
    }

    public function delete()
    {
        try {
            $jabatan = JabatanModel::findOrFail($this->jabatanToDelete);
            $jabatan->delete();
            session()->flash('success', 'Jabatan berhasil dihapus!');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus jabatan: ' . $e->getMessage());
            $this->closeDeleteModal();
        }
    }

    public function render()
    {
        $query = JabatanModel::query();
        if ($this->search) {
            $query->where('jabatan', 'like', '%' . $this->search . '%')
                  ->orWhere('unit', 'like', '%' . $this->search . '%');
        }
        $jabatans = $query->orderBy('jabatan')->paginate(10);

        return view('livewire.jabatan.index', [
            'jabatans' => $jabatans,
            'isEditMode' => $this->isEditMode,
            'showModal' => $this->showModal,
            'showDeleteModal' => $this->showDeleteModal,
            'form' => $this->form,
            'modalMessage' => $this->modalMessage,
            'modalMessageType' => $this->modalMessageType,
        ])->layout('components.layouts.app', ['title' => __('Manajemen Jabatan')]);
    }
}