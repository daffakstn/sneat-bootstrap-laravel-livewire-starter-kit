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
        if (!Auth::user()->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }
        $this->resetForm();
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
        $this->resetForm();
        $this->showModal = true;
        $this->dispatch('show-modal');
    }

    public function edit($id)
    {
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
        $validated = $this->validate([
            'form.tahun_id' => ['required', 'exists:tahun,id'],
            'form.lembaga_akreditasi_id' => ['required', 'exists:lembaga_akreditasi,id'],
            'form.standar_nasional_id' => ['required', 'exists:standar_nasional,id'],
            'form.status' => ['required', 'in:aktif,draft,nonaktif'],
            'form.nilai_mutu' => ['nullable', 'numeric', 'min:0', 'max:4', 'regex:/^\d+(\.\d{1,2})?$/'],
            'form.bukti_dokumen' => ['nullable', 'string', 'max:500'],
            'form.komentar_auditee' => ['nullable', 'string'],
            'form.komentar_auditor' => ['nullable', 'string'],
            'bukti_file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // 5MB max
        ], [
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
        ]);

        try {
            // Handle file upload if provided
            $buktiDokumen = $validated['form']['bukti_dokumen'];
            if ($this->bukti_file) {
                $fileName = time() . '_' . $this->bukti_file->getClientOriginalName();
                $path = $this->bukti_file->storeAs('bukti-dokumen', $fileName, 'public');
                $buktiDokumen = $path;
            }
            
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
                // In edit mode, we only update the existing record
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
                // Create new record
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

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
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
        ])->layout('components.layouts.app', ['title' => __('Daftar Standar Mutu')]);
    }
}