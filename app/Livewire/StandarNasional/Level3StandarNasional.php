<?php

namespace App\Livewire\StandarNasional;

use App\Models\StandarNasional as StandarNasionalModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Level3StandarNasional extends Component
{
    use WithPagination;

    public $parentId;
    public $showModal = false;
    public $showDeleteModal = false;
    public $isEditMode = false;
    public $standarId = null;
    public $standarToDelete = null;
    public $search = ''; // Added for table search
    public $modalMessage = null;
    public $modalMessageType = null;
    public $form = [
        'standar' => '',
        'keterangan' => '',
    ];

    public function mount($parentId)
    {
        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }
        $this->parentId = $parentId;
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
            'standar' => '',
            'keterangan' => '',
        ];
        $this->isEditMode = false;
        $this->standarId = null;
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->standarToDelete = null;
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
        $standar = StandarNasionalModel::findOrFail($id);
        if ($standar->level() !== 3) {
            session()->flash('error', 'Hanya standar level 3 yang dapat diedit di halaman ini.');
            return;
        }
        $this->form = [
            'standar' => $standar->standar,
            'keterangan' => $standar->keterangan,
        ];
        $this->standarId = $id;
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
        $standar = StandarNasionalModel::findOrFail($id);
        if ($standar->level() !== 3) {
            session()->flash('error', 'Hanya standar level 3 yang dapat dihapus di halaman ini.');
            return;
        }
        $this->standarToDelete = $id;
        $this->showDeleteModal = true;
        $this->dispatch('show-delete-modal');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->standarToDelete = null;
        $this->dispatch('hide-delete-modal');
    }

    public function save()
    {
        // Clear previous modal messages
        $this->modalMessage = null;
        $this->modalMessageType = null;

        // Clean and normalize the form data
        $this->form['standar'] = trim($this->form['standar']);
        $this->form['keterangan'] = trim($this->form['keterangan']);

        $validated = $this->validate([
            'form.standar' => ['required', 'string', 'max:50', 'unique:standar_nasional,standar,' . ($this->standarId ?: 'NULL') . ',id,parent_id,' . $this->parentId],
            'form.keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'form.standar.required' => 'Nama standar harus diisi.',
            'form.standar.string' => 'Nama standar harus berupa teks.',
            'form.standar.max' => 'Nama standar maksimal 50 karakter.',
            'form.standar.unique' => 'Nama standar sudah ada untuk level ini.',
            'form.keterangan.string' => 'Keterangan harus berupa teks.',
            'form.keterangan.max' => 'Keterangan maksimal 255 karakter.',
        ]);

        try {
            if ($this->isEditMode) {
                $standar = StandarNasionalModel::findOrFail($this->standarId);
                $standar->update([
                    'standar' => $validated['form']['standar'],
                    'keterangan' => $validated['form']['keterangan'],
                    'parent_id' => $this->parentId,
                ]);
                session()->flash('success', 'Standar Level 3 berhasil diperbarui!');
            } else {
                StandarNasionalModel::create([
                    'standar' => $validated['form']['standar'],
                    'keterangan' => $validated['form']['keterangan'],
                    'parent_id' => $this->parentId,
                ]);
                session()->flash('success', 'Standar Level 3 berhasil ditambahkan!');
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
            $standar = StandarNasionalModel::findOrFail($this->standarToDelete);
            $standar->delete();
            session()->flash('success', 'Standar Level 3 berhasil dihapus!');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus Standar Level 3: ' . $e->getMessage());
            $this->closeDeleteModal();
        }
    }

    public function render()
    {
        $parent = StandarNasionalModel::findOrFail($this->parentId);
        $standars = StandarNasionalModel::where('parent_id', $this->parentId)
            ->when($this->search, function ($query) {
                $query->where('standar', 'like', '%' . $this->search . '%')
                      ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            })
            ->orderBy('standar', 'asc')
            ->paginate(10);

        return view('livewire.standar-nasional.level3', [
            'parent' => $parent,
            'standars' => $standars,
            'isEditMode' => $this->isEditMode,
            'showModal' => $this->showModal,
            'showDeleteModal' => $this->showDeleteModal,
            'form' => $this->form,
            'modalMessage' => $this->modalMessage,
            'modalMessageType' => $this->modalMessageType,
        ])->layout('components.layouts.app', ['title' => __('Standar Nasional Level 3 - ' . $parent->standar)]);
    }
}