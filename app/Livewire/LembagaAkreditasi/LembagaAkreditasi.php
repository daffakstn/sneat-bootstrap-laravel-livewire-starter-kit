<?php

namespace App\Livewire\LembagaAkreditasi;

use App\Models\LembagaAkreditasi as LembagaAkreditasiModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class LembagaAkreditasi extends Component
{
    use WithPagination;

    public $showModal = false;
    public $showDeleteModal = false;
    public $isEditMode = false;
    public $lembagaId = null;
    public $lembagaToDelete = null;
    public $search = ''; // Added for table search
    public $modalMessage = null;
    public $modalMessageType = null;
    public $form = [
        'lembaga' => '',
        'keterangan' => '',
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
            'lembaga' => '',
            'keterangan' => '',
        ];
        $this->isEditMode = false;
        $this->lembagaId = null;
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->lembagaToDelete = null;
        $this->search = '';
        $this->modalMessage = null;
        $this->modalMessageType = null;
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
        $lembaga = LembagaAkreditasiModel::findOrFail($id);
        $this->form = [
            'lembaga' => $lembaga->lembaga,
            'keterangan' => $lembaga->keterangan,
        ];
        $this->lembagaId = $id;
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
        $this->lembagaToDelete = $id;
        $this->showDeleteModal = true;
        $this->dispatch('show-delete-modal');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->lembagaToDelete = null;
        $this->dispatch('hide-delete-modal');
    }

    public function save()
    {
        // Clear previous modal messages
        $this->modalMessage = null;
        $this->modalMessageType = null;

        // Clean and normalize the form data
        $this->form['lembaga'] = trim($this->form['lembaga']);
        $this->form['keterangan'] = trim($this->form['keterangan']);

        $validated = $this->validate([
            'form.lembaga' => ['required', 'string', 'max:50', 'unique:lembaga_akreditasi,lembaga,' . ($this->lembagaId ?: 'NULL')],
            'form.keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'form.lembaga.required' => 'Nama lembaga harus diisi.',
            'form.lembaga.string' => 'Nama lembaga harus berupa teks.',
            'form.lembaga.max' => 'Nama lembaga maksimal 50 karakter.',
            'form.lembaga.unique' => 'Nama lembaga sudah ada.',
            'form.keterangan.string' => 'Keterangan harus berupa teks.',
            'form.keterangan.max' => 'Keterangan maksimal 255 karakter.',
        ]);

        try {
            if ($this->isEditMode) {
                $lembaga = LembagaAkreditasiModel::findOrFail($this->lembagaId);
                $lembaga->update([
                    'lembaga' => $validated['form']['lembaga'],
                    'keterangan' => $validated['form']['keterangan'],
                ]);
                session()->flash('success', 'Lembaga Akreditasi berhasil diperbarui!');
            } else {
                LembagaAkreditasiModel::create([
                    'lembaga' => $validated['form']['lembaga'],
                    'keterangan' => $validated['form']['keterangan'],
                ]);
                session()->flash('success', 'Lembaga Akreditasi berhasil ditambahkan!');
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
            $lembaga = LembagaAkreditasiModel::findOrFail($this->lembagaToDelete);
            $lembaga->delete();
            session()->flash('success', 'Lembaga Akreditasi berhasil dihapus!');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus Lembaga Akreditasi: ' . $e->getMessage());
            $this->closeDeleteModal();
        }
    }

    public function render()
    {
        $lembagas = LembagaAkreditasiModel::when($this->search, function ($query) {
            $query->where('lembaga', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
        })
        ->orderBy('lembaga', 'asc')
        ->paginate(10);

        return view('livewire.lembaga-akreditasi.index', [
            'lembagas' => $lembagas,
            'isEditMode' => $this->isEditMode,
            'showModal' => $this->showModal,
            'showDeleteModal' => $this->showDeleteModal,
            'form' => $this->form,
            'modalMessage' => $this->modalMessage,
            'modalMessageType' => $this->modalMessageType,
        ])->layout('components.layouts.app', ['title' => __('Manajemen Lembaga Akreditasi')]);
    }
}