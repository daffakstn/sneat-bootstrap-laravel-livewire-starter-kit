<?php

namespace App\Livewire\StandarMutu;

use App\Models\StandarMutu as StandarMutuModel;
use App\Models\SubStandarMutu as SubStandarMutuModel;
use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SubStandarMutu extends Component
{
    use WithPagination;

    public $standarMutuId;
    public $showModal = false;
    public $showDeleteModal = false;
    public $isEditMode = false;
    public $subStandarId = null;
    public $subStandarToDelete = null;
    public $search = '';
    public $modalMessage = '';
    public $modalMessageType = 'success';
    
    // Prodi search properties
    public $prodiSearch = '';
    public $showProdiDropdown = false;
    
    // Multiselect properties for Prodi
    public $selectedProdis = [];
    public $selectedProdiNames = [];
    
    public $form = [
        'sub_standar' => '',
        'indikator_0' => '',
        'indikator_1' => '',
        'indikator_2' => '',
        'indikator_3' => '',
        'indikator_4' => '',
        'target' => '',
    ];
    public $indikatorViewData = null;
    public $showViewIndikatorModal = false;

    protected $messages = [
        'form.sub_standar.required' => 'Nama Sub Standar harus diisi.',
        'form.sub_standar.string' => 'Nama Sub Standar harus berupa teks.',
        'form.sub_standar.max' => 'Nama Sub Standar maksimal 255 karakter.',
        'form.indikator_0.required' => 'Indikator Level 0 harus diisi.',
        'form.indikator_0.string' => 'Indikator Level 0 harus berupa teks.',
        'form.indikator_1.required' => 'Indikator Level 1 harus diisi.',
        'form.indikator_1.string' => 'Indikator Level 1 harus berupa teks.',
        'form.indikator_2.required' => 'Indikator Level 2 harus diisi.',
        'form.indikator_2.string' => 'Indikator Level 2 harus berupa teks.',
        'form.indikator_3.required' => 'Indikator Level 3 harus diisi.',
        'form.indikator_3.string' => 'Indikator Level 3 harus berupa teks.',
        'form.indikator_4.required' => 'Indikator Level 4 harus diisi.',
        'form.indikator_4.string' => 'Indikator Level 4 harus berupa teks.',
        'selectedProdis.required' => 'Minimal satu Program Studi harus dipilih.',
        'selectedProdis.array' => 'Program Studi harus berupa array.',
        'selectedProdis.min' => 'Minimal satu Program Studi harus dipilih.',
        'selectedProdis.*.exists' => 'Program Studi yang dipilih tidak valid.',
    ];

    public function mount($standarMutu)
    {
        // Check if user has any of the allowed roles
        if (!Auth::user()->hasAnyRole(['Admin', 'Auditee', 'Auditor', 'Pimpinan'])) {
            abort(403, 'Unauthorized action.');
        }
        $this->standarMutuId = $standarMutu;
        $this->resetForm();
    }

    private function userHasAnyRole($roles)
    {
        foreach ($roles as $role) {
            if (Auth::user()->hasRole($role)) {
                return true;
            }
        }
        return false;
    }

    // Add helper methods to check user roles
    public function canFullAccess()
    {
        return Auth::user()->hasRole('Admin');
    }

    public function isReadOnly()
    {
        return Auth::user()->hasRole('Auditee') || 
               Auth::user()->hasRole('Auditor') || 
               Auth::user()->hasRole('Pimpinan');
    }

    public function canEdit()
    {
        return $this->canFullAccess();
    }

    public function canDelete()
    {
        return $this->canFullAccess();
    }

    public function canCreate()
    {
        return $this->canFullAccess();
    }

    public function updatedProdiSearch()
    {
        $this->showProdiDropdown = !empty($this->prodiSearch);
    }

    public function selectProdi($id, $prodi)
    {
        // Add to multiselect if not already selected
        if (!in_array($id, $this->selectedProdis)) {
            $this->selectedProdis[] = $id;
            $this->selectedProdiNames[] = $prodi;
        }
        $this->prodiSearch = '';
        $this->showProdiDropdown = false;
    }

    public function removeProdi($index)
    {
        unset($this->selectedProdis[$index]);
        unset($this->selectedProdiNames[$index]);
        $this->selectedProdis = array_values($this->selectedProdis);
        $this->selectedProdiNames = array_values($this->selectedProdiNames);
    }

    public function selectAllProdi()
    {
        // Ambil semua prodi yang belum dipilih
        $allProdis = Prodi::whereNotIn('id', $this->selectedProdis)->get();
        foreach ($allProdis as $prodi) {
            $this->selectedProdis[] = $prodi->id;
            $this->selectedProdiNames[] = $prodi->nama_prodi;
        }
        $this->prodiSearch = '';
        $this->showProdiDropdown = false;
    }

    public function getFilteredProdisProperty()
    {
        return Prodi::when($this->prodiSearch, function ($query) {
            $query->where('nama_prodi', 'like', '%' . $this->prodiSearch . '%');
        })
        ->whereNotIn('id', $this->selectedProdis) // Exclude already selected prodis
        ->limit(10)->get();
    }

    public function resetForm()
    {
        $this->resetErrorBag();
        $this->form = [
            'sub_standar' => '',
            'indikator_0' => '',
            'indikator_1' => '',
            'indikator_2' => '',
            'indikator_3' => '',
            'indikator_4' => '',
            'target' => '',
        ];
        $this->prodiSearch = '';
        $this->showProdiDropdown = false;
        $this->selectedProdis = [];
        $this->selectedProdiNames = [];
        $this->isEditMode = false;
        $this->subStandarId = null;
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->subStandarToDelete = null;
        $this->modalMessage = '';
        $this->modalMessageType = 'success';
        $this->resetPage();
    }

    public function create()
    {
        if (!$this->canCreate()) {
            abort(403, 'Unauthorized action.');
        }
        $this->resetForm();
        $this->showModal = true;
        $this->dispatch('show-modal');
    }

    public function edit($id)
    {
        if (!$this->canEdit()) {
            abort(403, 'Unauthorized action.');
        }
        
        $subStandar = SubStandarMutuModel::with('prodi')->findOrFail($id);
        $this->form = [
            'sub_standar' => $subStandar->sub_standar,
            'indikator_0' => $subStandar->indikator_0,
            'indikator_1' => $subStandar->indikator_1,
            'indikator_2' => $subStandar->indikator_2,
            'indikator_3' => $subStandar->indikator_3,
            'indikator_4' => $subStandar->indikator_4,
            'target' => $subStandar->target,
        ];
        $this->selectedProdis = [$subStandar->prodi_id];
        $this->selectedProdiNames = [$subStandar->prodi->nama_prodi];
        $this->subStandarId = $id;
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
        if (!$this->canDelete()) {
            abort(403, 'Unauthorized action.');
        }
        $this->subStandarToDelete = $id;
        $this->showDeleteModal = true;
        $this->dispatch('show-delete-modal');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->subStandarToDelete = null;
        $this->dispatch('hide-delete-modal');
    }

    public function save()
    {
        if (!$this->canEdit()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Validate that at least one prodi is selected
        if (empty($this->selectedProdis)) {
            session()->flash('error', 'Minimal satu Program Studi harus dipilih.');
            return;
        }

        $validated = $this->validate([
            'form.sub_standar' => ['required', 'string', 'max:255'],
            'form.indikator_0' => ['required', 'string'],
            'form.indikator_1' => ['required', 'string'],
            'form.indikator_2' => ['required', 'string'],
            'form.indikator_3' => ['required', 'string'],
            'form.indikator_4' => ['required', 'string'],
            'form.target' => ['nullable', 'string'],
            'selectedProdis' => ['required', 'array', 'min:1'],
            'selectedProdis.*' => ['exists:prodi,id'],
        ]);

        try {
            $createdCount = 0;
            $updatedCount = 0;

            if ($this->isEditMode) {
                // In edit mode, we only update the existing record
                $subStandar = SubStandarMutuModel::findOrFail($this->subStandarId);
                $subStandar->update([
                    'prodi_id' => $this->selectedProdis[0], // Use first selected prodi for edit
                    'sub_standar' => $validated['form']['sub_standar'],
                    'indikator_0' => $validated['form']['indikator_0'],
                    'indikator_1' => $validated['form']['indikator_1'],
                    'indikator_2' => $validated['form']['indikator_2'],
                    'indikator_3' => $validated['form']['indikator_3'],
                    'indikator_4' => $validated['form']['indikator_4'],
                    'target' => $validated['form']['target'],
                ]);
                $updatedCount = 1;
                $this->modalMessage = 'Sub Standar berhasil diperbarui!';
                $this->modalMessageType = 'success';
                session()->flash('success', 'Sub Standar berhasil diperbarui!');
            } else {
                // Create new records for each selected prodi
                foreach ($this->selectedProdis as $prodiId) {
                    // Check for duplicate sub-standar for the same prodi in this standar mutu
                    $duplicateQuery = SubStandarMutuModel::where('standar_mutu_id', $this->standarMutuId)
                        ->where('prodi_id', $prodiId)
                        ->where('sub_standar', $validated['form']['sub_standar']);
                    
                    if ($duplicateQuery->exists()) {
                        $prodiName = Prodi::find($prodiId)->nama_prodi;
                        session()->flash('error', "Sub standar untuk Program Studi '{$prodiName}' sudah ada.");
                        return;
                    }

                    SubStandarMutuModel::create([
                        'standar_mutu_id' => $this->standarMutuId,
                        'prodi_id' => $prodiId,
                        'sub_standar' => $validated['form']['sub_standar'],
                        'indikator_0' => $validated['form']['indikator_0'],
                        'indikator_1' => $validated['form']['indikator_1'],
                        'indikator_2' => $validated['form']['indikator_2'],
                        'indikator_3' => $validated['form']['indikator_3'],
                        'indikator_4' => $validated['form']['indikator_4'],
                        'target' => $validated['form']['target'],
                    ]);
                    $createdCount++;
                }
                
                if ($createdCount > 1) {
                    $this->modalMessage = "Berhasil menambahkan {$createdCount} Sub Standar untuk {$createdCount} Program Studi!";
                    session()->flash('success', "Berhasil menambahkan {$createdCount} Sub Standar untuk {$createdCount} Program Studi!");
                } else {
                    $this->modalMessage = 'Sub Standar berhasil ditambahkan!';
                    session()->flash('success', 'Sub Standar berhasil ditambahkan!');
                }
                $this->modalMessageType = 'success';
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
        if (!$this->canDelete()) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $subStandar = SubStandarMutuModel::findOrFail($this->subStandarToDelete);
            $subStandar->delete();
            session()->flash('success', 'Sub Standar berhasil dihapus!');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus Sub Standar: ' . $e->getMessage());
            $this->closeDeleteModal();
        }
    }

    public function showIndikator($id)
    {
        $subStandar = SubStandarMutuModel::findOrFail($id);
        $this->indikatorViewData = [
            'sub_standar' => $subStandar->sub_standar,
            'indikator_0' => $subStandar->indikator_0,
            'indikator_1' => $subStandar->indikator_1,
            'indikator_2' => $subStandar->indikator_2,
            'indikator_3' => $subStandar->indikator_3,
            'indikator_4' => $subStandar->indikator_4,
            'target' => $subStandar->target,
        ];
        $this->showViewIndikatorModal = true;
        $this->dispatch('show-view-indikator-modal');
    }

    public function closeViewIndikatorModal()
    {
        $this->showViewIndikatorModal = false;
        $this->indikatorViewData = null;
        $this->dispatch('hide-view-indikator-modal');
    }

    public function render()
    {
        $query = SubStandarMutuModel::with('prodi')->where('standar_mutu_id', $this->standarMutuId);
        if ($this->search) {
            $query->where('sub_standar', 'like', '%' . $this->search . '%');
        }
        $subStandars = $query->orderBy('sub_standar')->paginate(10);

        return view('livewire.standar-mutu.sub-standar-mutu', [
            'standar' => StandarMutuModel::with(['tahun', 'lembagaAkreditasi', 'standarNasional'])->findOrFail($this->standarMutuId),
            'subStandars' => $subStandars,
            'isEditMode' => $this->isEditMode,
            'showModal' => $this->showModal,
            'showDeleteModal' => $this->showDeleteModal,
            'form' => $this->form,
            'modalMessage' => $this->modalMessage,
            'modalMessageType' => $this->modalMessageType,
            'indikatorViewData' => $this->indikatorViewData,
            'showViewIndikatorModal' => $this->showViewIndikatorModal,
            'filteredProdis' => $this->filteredProdis,
            'showProdiDropdown' => $this->showProdiDropdown,
            'selectedProdis' => $this->selectedProdis,
            'selectedProdiNames' => $this->selectedProdiNames,
            // Role-based access control
            'canFullAccess' => $this->canFullAccess(),
            'isReadOnly' => $this->isReadOnly(),
            'canEdit' => $this->canEdit(),
            'canDelete' => $this->canDelete(),
            'canCreate' => $this->canCreate(),
        ])->layout('components.layouts.app', ['title' => __('Sub Standar Mutu')]);
    }
}