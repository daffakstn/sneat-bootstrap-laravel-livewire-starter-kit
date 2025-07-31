<?php

namespace App\Livewire\StandarMutu;

use App\Models\StandarMutu as StandarMutuModel;
use App\Models\Tahun;
use App\Models\LembagaAkreditasi;
use App\Models\StandarNasional;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class StandarMutu extends Component
{
    use WithPagination, WithFileUploads;

    public $showModal = false;
    public $showDeleteModal = false;
    public $showViewModal = false;
    public $isEditMode = false;
    public $standarMutuId = null;
    public $standarToDelete = null;
    public $selectedStandar = null;
    public $search = '';
    
    // Search properties for dropdowns
    public $tahunSearch = '';
    public $lembagaSearch = '';
    public $standarSearch = '';
    
    // Dropdown visibility
    public $showTahunDropdown = false;
    public $showLembagaDropdown = false;
    public $showStandarDropdown = false;
    
    public $form = [
        'tahun_id' => '',
        'lembaga_akreditasi_id' => '',
        'standar_nasional_id' => '',
        'status' => 'draft',
        'nilai_mutu' => '',
        'bukti_dokumen' => '',
        'komentar_auditee' => '',
        'komentar_auditor' => '',
    ];
    
    public $bukti_file = null;

    protected $listeners = ['hideDropdown'];

    public function mount()
    {
        // Check if user has any of the allowed roles
        if (!Auth::user()->hasAnyRole(['Admin', 'Auditee', 'Auditor', 'Pimpinan'])) {
            abort(403, 'Unauthorized action.');
        }
        $this->resetForm();
    }

    // Add helper methods to check user roles
    public function canFullAccess()
    {
        return Auth::user()->hasRole('Admin');
    }

    public function canUploadAndCommentAuditee()
    {
        return Auth::user()->hasRole('Auditee');
    }

    public function canCommentAuditor()
    {
        return Auth::user()->hasRole('Auditor');
    }

    public function isReadOnly()
    {
        return Auth::user()->hasRole('Pimpinan');
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

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTahunSearch()
    {
        $this->showTahunDropdown = !empty($this->tahunSearch);
    }

    public function updatedLembagaSearch()
    {
        $this->showLembagaDropdown = !empty($this->lembagaSearch);
    }

    public function updatedStandarSearch()
    {
        $this->showStandarDropdown = !empty($this->standarSearch);
    }

    public function updatedBuktiFile()
    {
        $this->validate([
            'bukti_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'bukti_file.file' => 'File yang diupload harus berupa file.',
            'bukti_file.mimes' => 'File yang diupload harus berupa PDF.',
            'bukti_file.max' => 'Ukuran file maksimal 5MB.',
        ]);
    }

    public function hideDropdown($type)
    {
        switch ($type) {
            case 'tahun':
                $this->showTahunDropdown = false;
                break;
            case 'lembaga':
                $this->showLembagaDropdown = false;
                break;
            case 'standar':
                $this->showStandarDropdown = false;
                break;
        }
    }

    public function selectTahun($id, $tahun)
    {
        $this->form['tahun_id'] = $id;
        $this->tahunSearch = $tahun;
        $this->showTahunDropdown = false;
    }

    public function selectLembaga($id, $lembaga)
    {
        $this->form['lembaga_akreditasi_id'] = $id;
        $this->lembagaSearch = $lembaga;
        $this->showLembagaDropdown = false;
    }

    public function selectStandar($id, $standar)
    {
        $this->form['standar_nasional_id'] = $id;
        $this->standarSearch = $standar;
        $this->showStandarDropdown = false;
    }

    public function getFilteredTahunsProperty()
    {
        return Tahun::when($this->tahunSearch, function ($query) {
            $query->where('tahun', 'like', '%' . $this->tahunSearch . '%');
        })->limit(10)->get();
    }

    public function getFilteredLembagaAkreditasisProperty()
    {
        return LembagaAkreditasi::when($this->lembagaSearch, function ($query) {
            $query->where('lembaga', 'like', '%' . $this->lembagaSearch . '%');
        })->limit(10)->get();
    }

    public function getFilteredStandarNasionalsProperty()
    {
        return StandarNasional::with(['parent.parent'])
            ->whereNotNull('parent_id')
            ->whereHas('parent', function ($query) {
                $query->whereNotNull('parent_id');
            })
            ->when($this->standarSearch, function ($query) {
                $query->where('standar', 'like', '%' . $this->standarSearch . '%')
                      ->orWhereHas('parent', function ($q) {
                          $q->where('standar', 'like', '%' . $this->standarSearch . '%')
                            ->orWhereHas('parent', function ($qq) {
                                $qq->where('standar', 'like', '%' . $this->standarSearch . '%');
                            });
                      });
            })
            ->limit(15)->get();
    }

    public function resetForm()
    {
        $this->resetErrorBag();
        $this->form = [
            'tahun_id' => '',
            'lembaga_akreditasi_id' => '',
            'standar_nasional_id' => '',
            'status' => 'draft',
            'nilai_mutu' => '',
            'bukti_dokumen' => '',
            'komentar_auditee' => '',
            'komentar_auditor' => '',
        ];
        
        // Reset search fields
        $this->tahunSearch = '';
        $this->lembagaSearch = '';
        $this->standarSearch = '';
        
        // Reset dropdown visibility
        $this->showTahunDropdown = false;
        $this->showLembagaDropdown = false;
        $this->showStandarDropdown = false;
        
        // Reset file upload
        $this->bukti_file = null;
        
        $this->isEditMode = false;
        $this->standarMutuId = null;
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->showViewModal = false;
        $this->standarToDelete = null;
        $this->selectedStandar = null;
        $this->bukti_file = null;
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
        // Check if user can edit or if they can only edit specific fields
        if (!$this->canEdit() && !$this->canUploadAndCommentAuditee() && !$this->canCommentAuditor()) {
            abort(403, 'Unauthorized action.');
        }
        
        $standar = StandarMutuModel::with(['tahun', 'lembagaAkreditasi', 'standarNasional.parent.parent'])->findOrFail($id);
        
        $this->form = [
            'tahun_id' => $standar->tahun_id,
            'lembaga_akreditasi_id' => $standar->lembaga_akreditasi_id,
            'standar_nasional_id' => $standar->standar_nasional_id,
            'status' => $standar->status,
            'nilai_mutu' => $standar->nilai_mutu,
            'bukti_dokumen' => $standar->bukti_dokumen,
            'komentar_auditee' => $standar->komentar_auditee,
            'komentar_auditor' => $standar->komentar_auditor,
        ];
        
        // Set search fields with current values
        $this->tahunSearch = $standar->tahun->tahun;
        $this->lembagaSearch = $standar->lembagaAkreditasi->lembaga;
        $this->standarSearch = $standar->standarNasional->parent->parent->standar . ' > ' . 
                              $standar->standarNasional->parent->standar . ' > ' . 
                              $standar->standarNasional->standar;
        
        $this->standarMutuId = $id;
        $this->isEditMode = true;
        $this->showModal = true;
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

    public function view($id)
    {
        $this->selectedStandar = StandarMutuModel::with(['tahun', 'lembagaAkreditasi', 'standarNasional.parent.parent'])->findOrFail($id);
        $this->showViewModal = true;
        $this->dispatch('show-view-modal');
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedStandar = null;
        $this->dispatch('hide-view-modal');
    }

    public function save()
    {
        // Define validation rules based on user role
        $validationRules = [];
        $validationMessages = [];

        if ($this->canFullAccess()) {
            // Admin has full access to all fields
            $validationRules = [
                'form.tahun_id' => ['required', 'exists:tahun,id'],
                'form.lembaga_akreditasi_id' => ['required', 'exists:lembaga_akreditasi,id'],
                'form.standar_nasional_id' => ['required', 'exists:standar_nasional,id'],
                'form.status' => ['required', 'in:aktif,draft,nonaktif'],
                'form.nilai_mutu' => ['nullable', 'numeric', 'min:0', 'max:4', 'regex:/^\d+(\.\d{1,2})?$/'],
                'form.bukti_dokumen' => ['nullable', 'string', 'max:500'],
                'form.komentar_auditee' => ['nullable', 'string'],
                'form.komentar_auditor' => ['nullable', 'string'],
                'bukti_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            ];
            $validationMessages = [
                'form.tahun_id.required' => 'Tahun harus dipilih.',
                'form.tahun_id.exists' => 'Tahun yang dipilih tidak valid.',
                'form.lembaga_akreditasi_id.required' => 'Lembaga Akreditasi harus dipilih.',
                'form.lembaga_akreditasi_id.exists' => 'Lembaga Akreditasi yang dipilih tidak valid.',
                'form.standar_nasional_id.required' => 'Standar Nasional harus dipilih.',
                'form.standar_nasional_id.exists' => 'Standar Nasional yang dipilih tidak valid.',
                'form.status.required' => 'Status harus dipilih.',
                'form.status.in' => 'Status yang dipilih tidak valid.',
                'form.nilai_mutu.numeric' => 'Nilai mutu harus berupa angka.',
                'form.nilai_mutu.min' => 'Nilai mutu minimal adalah 0.',
                'form.nilai_mutu.max' => 'Nilai mutu maksimal adalah 4.',
                'form.nilai_mutu.regex' => 'Nilai mutu maksimal 2 digit desimal.',
                'form.bukti_dokumen.max' => 'Bukti dokumen maksimal 500 karakter.',
                'form.komentar_auditee.string' => 'Komentar auditee harus berupa teks.',
                'form.komentar_auditor.string' => 'Komentar auditor harus berupa teks.',
                'bukti_file.file' => 'File yang diupload harus berupa file.',
                'bukti_file.mimes' => 'File yang diupload harus berupa PDF.',
                'bukti_file.max' => 'Ukuran file maksimal 5MB.',
            ];
        } elseif ($this->canUploadAndCommentAuditee()) {
            // Auditee can upload bukti dokumen and add komentar auditee
            // When creating new records, they need to provide required fields
            if (!$this->isEditMode) {
                // Creating new record - require basic fields
                $validationRules = [
                    'form.tahun_id' => ['required', 'exists:tahun,id'],
                    'form.lembaga_akreditasi_id' => ['required', 'exists:lembaga_akreditasi,id'],
                    'form.standar_nasional_id' => ['required', 'exists:standar_nasional,id'],
                    'form.bukti_dokumen' => ['nullable', 'string', 'max:500'],
                    'form.komentar_auditee' => ['nullable', 'string'],
                    'bukti_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
                ];
                $validationMessages = [
                    'form.tahun_id.required' => 'Tahun harus dipilih.',
                    'form.tahun_id.exists' => 'Tahun yang dipilih tidak valid.',
                    'form.lembaga_akreditasi_id.required' => 'Lembaga Akreditasi harus dipilih.',
                    'form.lembaga_akreditasi_id.exists' => 'Lembaga Akreditasi yang dipilih tidak valid.',
                    'form.standar_nasional_id.required' => 'Standar Nasional harus dipilih.',
                    'form.standar_nasional_id.exists' => 'Standar Nasional yang dipilih tidak valid.',
                    'form.bukti_dokumen.max' => 'Bukti dokumen maksimal 500 karakter.',
                    'form.komentar_auditee.string' => 'Komentar auditee harus berupa teks.',
                    'bukti_file.file' => 'File yang diupload harus berupa file.',
                    'bukti_file.mimes' => 'File yang diupload harus berupa PDF.',
                    'bukti_file.max' => 'Ukuran file maksimal 5MB.',
                ];
            } else {
                // Editing existing record - only validate fields they can edit
                $validationRules = [
                    'form.bukti_dokumen' => ['nullable', 'string', 'max:500'],
                    'form.komentar_auditee' => ['nullable', 'string'],
                    'bukti_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
                ];
                $validationMessages = [
                    'form.bukti_dokumen.max' => 'Bukti dokumen maksimal 500 karakter.',
                    'form.komentar_auditee.string' => 'Komentar auditee harus berupa teks.',
                    'bukti_file.file' => 'File yang diupload harus berupa file.',
                    'bukti_file.mimes' => 'File yang diupload harus berupa PDF.',
                    'bukti_file.max' => 'Ukuran file maksimal 5MB.',
                ];
            }
        } elseif ($this->canCommentAuditor()) {
            // Auditor can only add komentar auditor
            $validationRules = [
                'form.komentar_auditor' => ['nullable', 'string'],
            ];
            $validationMessages = [
                'form.komentar_auditor.string' => 'Komentar auditor harus berupa teks.',
            ];
        } else {
            abort(403, 'Unauthorized action.');
        }

        $validated = $this->validate($validationRules, $validationMessages);

        try {
            // Handle file upload if provided
            $buktiDokumen = isset($validated['form']['bukti_dokumen']) ? $validated['form']['bukti_dokumen'] : null;
            if ($this->bukti_file) {
                $fileName = time() . '_' . $this->bukti_file->getClientOriginalName();
                $path = $this->bukti_file->storeAs('bukti-dokumen', $fileName, 'public');
                $buktiDokumen = $path;
            }
            
            if ($this->canFullAccess()) {
                // Admin can perform full CRUD operations
                
                // Validate that the selected standar_nasional_id is a Level 3 standard
                $standarNasional = StandarNasional::findOrFail($validated['form']['standar_nasional_id']);
                if ($standarNasional->level() !== 3) {
                    session()->flash('error', 'Hanya standar nasional level 3 yang dapat dipilih.');
                    return;
                }

                // Check for duplicate combinations
                $duplicateQuery = StandarMutuModel::where('tahun_id', $validated['form']['tahun_id'])
                    ->where('lembaga_akreditasi_id', $validated['form']['lembaga_akreditasi_id'])
                    ->where('standar_nasional_id', $validated['form']['standar_nasional_id']);
                
                if ($this->isEditMode) {
                    $duplicateQuery->where('id', '!=', $this->standarMutuId);
                }
                
                if ($duplicateQuery->exists()) {
                    session()->flash('error', 'Kombinasi data untuk standar mutu ini sudah ada.');
                    return;
                }

                // Validate status change to 'aktif'
                if ($validated['form']['status'] === 'aktif') {
                    if ($this->isEditMode) {
                        $standarMutu = StandarMutuModel::findOrFail($this->standarMutuId);
                        if ($standarMutu->subStandars()->count() === 0) {
                            session()->flash('error', 'Tidak dapat mengaktifkan standar tanpa sub-standar dan indikator.');
                            return;
                        }
                    }
                }

                if ($this->isEditMode) {
                    // Admin can update all fields
                    $standar = StandarMutuModel::findOrFail($this->standarMutuId);
                    $standar->update([
                        'tahun_id' => $validated['form']['tahun_id'],
                        'lembaga_akreditasi_id' => $validated['form']['lembaga_akreditasi_id'],
                        'standar_nasional_id' => $validated['form']['standar_nasional_id'],
                        'status' => $validated['form']['status'],
                        'nilai_mutu' => $validated['form']['nilai_mutu'],
                        'bukti_dokumen' => $buktiDokumen,
                        'komentar_auditee' => $validated['form']['komentar_auditee'],
                        'komentar_auditor' => $validated['form']['komentar_auditor'],
                    ]);
                    session()->flash('success', 'Standar Mutu berhasil diperbarui!');
                } else {
                    // Admin can create new record
                    StandarMutuModel::create([
                        'tahun_id' => $validated['form']['tahun_id'],
                        'lembaga_akreditasi_id' => $validated['form']['lembaga_akreditasi_id'],
                        'standar_nasional_id' => $validated['form']['standar_nasional_id'],
                        'status' => $validated['form']['status'],
                        'nilai_mutu' => $validated['form']['nilai_mutu'],
                        'bukti_dokumen' => $buktiDokumen,
                        'komentar_auditee' => $validated['form']['komentar_auditee'],
                        'komentar_auditor' => $validated['form']['komentar_auditor'],
                    ]);
                    session()->flash('success', 'Standar Mutu berhasil ditambahkan!');
                }
            } elseif ($this->canUploadAndCommentAuditee()) {
                // Auditee can create new records or update existing ones with bukti dokumen and komentar auditee
                if ($this->isEditMode) {
                    $standar = StandarMutuModel::findOrFail($this->standarMutuId);
                    $updateData = [];
                    
                    // Update bukti_dokumen if file was uploaded or link was provided
                    if ($buktiDokumen !== null) {
                        $updateData['bukti_dokumen'] = $buktiDokumen;
                    }
                    
                    // Update komentar_auditee if provided
                    if (isset($validated['form']['komentar_auditee'])) {
                        $updateData['komentar_auditee'] = $validated['form']['komentar_auditee'];
                    }
                    
                    if (!empty($updateData)) {
                        $standar->update($updateData);
                        session()->flash('success', 'Bukti dokumen dan komentar auditee berhasil diperbarui!');
                    } else {
                        session()->flash('info', 'Tidak ada perubahan yang disimpan.');
                    }
                } else {
                    // Allow auditee to create new records, but only with minimal required data
                    // They need to provide at least bukti_dokumen (file or link)
                    if (empty($buktiDokumen)) {
                        session()->flash('error', 'Auditee harus mengunggah bukti dokumen atau menyediakan link Google Drive.');
                        return;
                    }
                    
                    // Create new record with default values for required fields
                    // Note: This assumes that tahun_id, lembaga_akreditasi_id, and standar_nasional_id 
                    // are provided in the form even for auditee users
                    if (empty($validated['form']['tahun_id']) || 
                        empty($validated['form']['lembaga_akreditasi_id']) || 
                        empty($validated['form']['standar_nasional_id'])) {
                        session()->flash('error', 'Data tahun, lembaga akreditasi, dan standar nasional harus dipilih.');
                        return;
                    }
                    
                    StandarMutuModel::create([
                        'tahun_id' => $validated['form']['tahun_id'],
                        'lembaga_akreditasi_id' => $validated['form']['lembaga_akreditasi_id'],
                        'standar_nasional_id' => $validated['form']['standar_nasional_id'],
                        'status' => 'draft', // Default status for auditee
                        'nilai_mutu' => null, // Auditee cannot set nilai_mutu
                        'bukti_dokumen' => $buktiDokumen,
                        'komentar_auditee' => $validated['form']['komentar_auditee'] ?? null,
                        'komentar_auditor' => null,
                    ]);
                    session()->flash('success', 'Standar Mutu berhasil ditambahkan dengan bukti dokumen!');
                }
            } elseif ($this->canCommentAuditor()) {
                // Auditor can only update komentar auditor
                if ($this->isEditMode) {
                    $standar = StandarMutuModel::findOrFail($this->standarMutuId);
                    if (isset($validated['form']['komentar_auditor'])) {
                        $standar->update([
                            'komentar_auditor' => $validated['form']['komentar_auditor']
                        ]);
                        session()->flash('success', 'Komentar auditor berhasil diperbarui!');
                    }
                } else {
                    session()->flash('error', 'Auditor hanya dapat mengedit data yang sudah ada.');
                    return;
                }
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        if (!$this->canDelete()) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $standar = StandarMutuModel::findOrFail($this->standarToDelete);
            
            // Check if standar has sub-standards before deleting
            if ($standar->subStandars()->count() > 0) {
                session()->flash('error', 'Tidak dapat menghapus standar yang memiliki sub-standar. Hapus sub-standar terlebih dahulu.');
                $this->closeDeleteModal();
                return;
            }
            
            $standar->delete();
            session()->flash('success', 'Standar Mutu berhasil dihapus!');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus Standar Mutu: ' . $e->getMessage());
            $this->closeDeleteModal();
        }
    }

    public function render()
    {
        $standars = StandarMutuModel::with(['tahun', 'lembagaAkreditasi', 'standarNasional.parent.parent'])
            ->when($this->search, function ($query) {
                $query->whereHas('tahun', function ($q) {
                    $q->where('tahun', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('lembagaAkreditasi', function ($q) {
                    $q->where('lembaga', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('standarNasional', function ($q) {
                    $q->where('standar', 'like', '%' . $this->search . '%')
                      ->orWhereHas('parent', function ($qq) {
                          $qq->where('standar', 'like', '%' . $this->search . '%')
                             ->orWhereHas('parent', function ($qqq) {
                                 $qqq->where('standar', 'like', '%' . $this->search . '%');
                             });
                      });
                })
                ->orWhere('status', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.standar-mutu.index', [
            'standars' => $standars,
            'tahuns' => Tahun::orderBy('tahun', 'desc')->get(),
            'lembagaAkreditasis' => LembagaAkreditasi::orderBy('lembaga')->get(),
            'standarNasionals' => StandarNasional::with(['parent.parent'])
                ->whereNotNull('parent_id')
                ->whereHas('parent', function ($query) {
                    $query->whereNotNull('parent_id');
                })
                ->orderBy('standar')
                ->get(),
            'filteredTahuns' => $this->filteredTahuns,
            'filteredLembagaAkreditasis' => $this->filteredLembagaAkreditasis,
            'filteredStandarNasionals' => $this->filteredStandarNasionals,
            'isEditMode' => $this->isEditMode,
            'showModal' => $this->showModal,
            'showDeleteModal' => $this->showDeleteModal,
            'showViewModal' => $this->showViewModal,
            'selectedStandar' => $this->selectedStandar,
            'form' => $this->form,
            // Role-based access control
            'canFullAccess' => $this->canFullAccess(),
            'canUploadAndCommentAuditee' => $this->canUploadAndCommentAuditee(),
            'canCommentAuditor' => $this->canCommentAuditor(),
            'isReadOnly' => $this->isReadOnly(),
            'canEdit' => $this->canEdit(),
            'canDelete' => $this->canDelete(),
            'canCreate' => $this->canCreate(),
        ])->layout('components.layouts.app', ['title' => __('Daftar Standar Mutu')]);
    }
}