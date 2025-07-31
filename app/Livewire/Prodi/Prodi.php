<?php

namespace App\Livewire\Prodi;

use App\Models\Prodi as ProdiModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Prodi extends Component
{
    use WithPagination;

    public $showModal = false;
    public $showDeleteModal = false;
    public $isEditMode = false;
    public $prodiId = null;
    public $prodiToDelete = null;
    public $search = '';
    public $modalMessage = '';
    public $modalMessageType = 'success';
    public $form = [
        'nama_prodi' => '',
    ];

    protected $messages = [
        'form.nama_prodi.required' => 'Nama Program Studi harus diisi.',
        'form.nama_prodi.string' => 'Nama Program Studi harus berupa teks.',
        'form.nama_prodi.max' => 'Nama Program Studi maksimal 255 karakter.',
        'form.nama_prodi.unique' => 'Nama Program Studi sudah digunakan.',
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
            'nama_prodi' => '',
        ];
        $this->isEditMode = false;
        $this->prodiId = null;
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->prodiToDelete = null;
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
        $prodi = ProdiModel::findOrFail($id);
        $this->form = [
            'nama_prodi' => $prodi->nama_prodi,
        ];
        $this->prodiId = $id;
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
        $this->prodiToDelete = $id;
        $this->showDeleteModal = true;
        $this->dispatch('show-delete-modal');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->prodiToDelete = null;
        $this->dispatch('hide-delete-modal');
    }

    public function save()
    {
        $validated = $this->validate([
            'form.nama_prodi' => ['required', 'string', 'max:255', 'unique:prodi,nama_prodi,' . ($this->prodiId ?: 'NULL')],
        ]);

        try {
            if ($this->isEditMode) {
                $prodi = ProdiModel::findOrFail($this->prodiId);
                $prodi->update([
                    'nama_prodi' => $validated['form']['nama_prodi'],
                ]);
                $this->modalMessage = 'Program Studi berhasil diperbarui!';
                $this->modalMessageType = 'success';
                session()->flash('success', 'Program Studi berhasil diperbarui!');
            } else {
                ProdiModel::create([
                    'nama_prodi' => $validated['form']['nama_prodi'],
                ]);
                $this->modalMessage = 'Program Studi berhasil ditambahkan!';
                $this->modalMessageType = 'success';
                session()->flash('success', 'Program Studi berhasil ditambahkan!');
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
            $prodi = ProdiModel::findOrFail($this->prodiToDelete);
            $prodi->delete();
            session()->flash('success', 'Program Studi berhasil dihapus!');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus Program Studi: ' . $e->getMessage());
            $this->closeDeleteModal();
        }
    }

    public function render()
    {
        $query = ProdiModel::query();
        if ($this->search) {
            $query->where('nama_prodi', 'like', '%' . $this->search . '%');
        }
        $prodis = $query->orderBy('nama_prodi')->paginate(10);

        return view('livewire.prodi.index', [
            'prodis' => $prodis,
            'isEditMode' => $this->isEditMode,
            'showModal' => $this->showModal,
            'showDeleteModal' => $this->showDeleteModal,
            'form' => $this->form,
            'modalMessage' => $this->modalMessage,
            'modalMessageType' => $this->modalMessageType,
        ])->layout('components.layouts.app', ['title' => __('Manajemen Program Studi')]);
    }
}