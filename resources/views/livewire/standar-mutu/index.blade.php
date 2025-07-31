@section('title', __('Daftar Standar Mutu'))

<div class="row g-4">
    <div class="col-lg-12">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($isReadOnly)
            <div class="alert alert-info" role="alert">
                <i class="bx bx-info-circle me-2"></i>
                <strong>{{ __('Informasi:') }}</strong> {{ __('Anda hanya memiliki akses untuk melihat data.') }}
            </div>
        @elseif($canUploadAndCommentAuditee)
            <div class="alert alert-info" role="alert">
                <i class="bx bx-info-circle me-2"></i>
                <strong>{{ __('Informasi:') }}</strong> {{ __('Anda dapat mengedit bukti dokumen dan komentar auditee.') }}
            </div>
        @elseif($canCommentAuditor)
            <div class="alert alert-info" role="alert">
                <i class="bx bx-info-circle me-2"></i>
                <strong>{{ __('Informasi:') }}</strong> {{ __('Anda dapat mengedit komentar auditor.') }}
            </div>
        @endif

        <!-- Modal Form -->
        <div class="modal fade" id="standarMutuModal" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi {{ $isEditMode ? 'bi-pencil-square' : 'bi-plus-circle' }} me-2" viewBox="0 0 16 16">
                                @if($isEditMode)
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                @else
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                                @endif
                            </svg>
                            {{ $isEditMode ? __('Edit Standar Mutu') : __('Tambah Standar Mutu') }}
                        </h5>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <!-- Informasi Dasar -->
                            <div class="row g-3 mb-4">
                                
                                <!-- Tahun Field -->
                                @if($canFullAccess)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tahun_id" class="form-label fw-medium">{{ __('Tahun') }} <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" 
                                                   class="form-control @error('form.tahun_id') is-invalid @enderror" 
                                                   placeholder="{{ __('Cari dan pilih tahun...') }}"
                                                   wire:model.live="tahunSearch"
                                                   wire:focus="$set('showTahunDropdown', true)"
                                                   autocomplete="off">
                                            <i class="bx bx-search position-absolute top-50 end-0 translate-middle-y me-3"></i>
                                            
                                            @if($showTahunDropdown && count($filteredTahuns) > 0)
                                                <div class="dropdown-menu show w-100 mt-1" style="max-height: 200px; overflow-y: auto;">
                                                    @foreach($filteredTahuns as $tahun)
                                                        <a class="dropdown-item" 
                                                           href="javascript:void(0);" 
                                                           wire:click="selectTahun({{ $tahun->id }}, '{{ $tahun->tahun }}')">
                                                            {{ $tahun->tahun }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        @error('form.tahun_id') 
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                <!-- Lembaga Akreditasi Field -->
                                @if($canFullAccess)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="lembaga_akreditasi_id" class="form-label fw-medium">{{ __('Lembaga Akreditasi') }} <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" 
                                                   class="form-control @error('form.lembaga_akreditasi_id') is-invalid @enderror" 
                                                   placeholder="{{ __('Cari dan pilih lembaga akreditasi...') }}"
                                                   wire:model.live="lembagaSearch"
                                                   wire:focus="$set('showLembagaDropdown', true)"
                                                   autocomplete="off">
                                            <i class="bx bx-search position-absolute top-50 end-0 translate-middle-y me-3"></i>
                                            
                                            @if($showLembagaDropdown && count($filteredLembagaAkreditasis) > 0)
                                                <div class="dropdown-menu show w-100 mt-1" style="max-height: 200px; overflow-y: auto;">
                                                    @foreach($filteredLembagaAkreditasis as $lembaga)
                                                        <a class="dropdown-item" 
                                                           href="javascript:void(0);" 
                                                           wire:click="selectLembaga({{ $lembaga->id }}, '{{ $lembaga->lembaga }}')">
                                                            {{ $lembaga->lembaga }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        @error('form.lembaga_akreditasi_id') 
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                <!-- Standar Nasional Field -->
                                @if($canFullAccess)
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="standar_nasional_id" class="form-label fw-medium">{{ __('Standar Nasional') }} <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" 
                                                   class="form-control @error('form.standar_nasional_id') is-invalid @enderror" 
                                                   placeholder="{{ __('Cari dan pilih standar nasional...') }}"
                                                   wire:model.live="standarSearch"
                                                   wire:focus="$set('showStandarDropdown', true)"
                                                   autocomplete="off">
                                            <i class="bx bx-search position-absolute top-50 end-0 translate-middle-y me-3"></i>
                                            
                                            @if($showStandarDropdown && count($filteredStandarNasionals) > 0)
                                                <div class="dropdown-menu show w-100 mt-1" style="max-height: 250px; overflow-y: auto;">
                                                    @foreach($filteredStandarNasionals as $standar)
                                                        <a class="dropdown-item" 
                                                           href="javascript:void(0);" 
                                                           wire:click="selectStandar({{ $standar->id }}, '{{ $standar->parent->parent->standar }} > {{ $standar->parent->standar }} > {{ $standar->standar }}')">
                                                            <div class="d-flex flex-column">
                                                                <span class="fw-medium">{{ $standar->standar }}</span>
                                                                <small class="text-muted">{{ $standar->parent->parent->standar }} > {{ $standar->parent->standar }}</small>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        @error('form.standar_nasional_id') 
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Status dan Nilai -->
                            @if($canFullAccess)
                            <div class="row g-3 mb-4">
                                
                                <!-- Status Field -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label fw-medium">{{ __('Status') }} <span class="text-danger">*</span></label>
                                        <div class="dropdown w-100">
                                            <button class="btn btn-outline-secondary dropdown-toggle w-100 d-flex justify-content-between align-items-center @error('form.status') is-invalid @enderror" 
                                                    type="button" 
                                                    data-bs-toggle="dropdown" 
                                                    aria-expanded="false">
                                                <span class="text-start">
                                                    @if($form['status'])
                                                        @if($form['status'] == 'draft')
                                                            {{ __('Draft') }}
                                                        @elseif($form['status'] == 'aktif')
                                                            {{ __('Aktif') }}
                                                        @elseif($form['status'] == 'nonaktif')
                                                            {{ __('Nonaktif') }}
                                                        @endif
                                                    @else
                                                        {{ __('Pilih Status') }}
                                                    @endif
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu w-100">
                                                <li><a class="dropdown-item" href="javascript:void(0);" wire:click="$set('form.status', '')">{{ __('Pilih Status') }}</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);" wire:click="$set('form.status', 'draft')">{{ __('Draft') }}</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);" wire:click="$set('form.status', 'aktif')">{{ __('Aktif') }}</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);" wire:click="$set('form.status', 'nonaktif')">{{ __('Nonaktif') }}</a></li>
                                            </ul>
                                        </div>
                                        @error('form.status') 
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nilai Mutu Field -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nilai_mutu" class="form-label fw-medium">{{ __('Nilai Mutu') }}</label>
                                        <input type="number" 
                                               class="form-control @error('form.nilai_mutu') is-invalid @enderror" 
                                               placeholder="{{ __('Masukkan nilai mutu (0-4)') }}"
                                               wire:model="form.nilai_mutu"
                                               step="0.01"
                                               min="0"
                                               max="4">
                                        <div class="form-text">{{ __('Nilai mutu maksimal adalah 4.00') }}</div>
                                        @error('form.nilai_mutu') 
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Bukti Dokumen -->
                            @if($canFullAccess || $canUploadAndCommentAuditee)
                            <div class="row g-3 mb-4">
                                
                                <div class="col-12">
                                    <div class="mb-3">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium">{{ __('Upload File PDF') }}</label>
                                                <input type="file" 
                                                       class="form-control @error('bukti_file') is-invalid @enderror" 
                                                       accept=".pdf"
                                                       wire:model="bukti_file">
                                                <div class="form-text">{{ __('Maksimal 5MB') }}</div>
                                                @error('bukti_file') 
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-medium">{{ __('Atau Input Link Google Drive') }}</label>
                                                <input type="url" 
                                                       class="form-control @error('form.bukti_dokumen') is-invalid @enderror" 
                                                       placeholder="{{ __('https://drive.google.com/...') }}"
                                                       wire:model="form.bukti_dokumen">
                                                <div class="form-text">{{ __('Pilih salah satu: upload file PDF atau input link Google Drive') }}</div>
                                                @error('form.bukti_dokumen') 
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Komentar -->
                            <div class="row g-3">
                                
                                <!-- Komentar Auditee Field -->
                                @if($canFullAccess || $canUploadAndCommentAuditee)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="komentar_auditee" class="form-label fw-medium">{{ __('Komentar Auditee') }}</label>
                                        <textarea class="form-control @error('form.komentar_auditee') is-invalid @enderror" 
                                                  rows="4"
                                                  placeholder="{{ __('Masukkan komentar auditee...') }}"
                                                  wire:model="form.komentar_auditee"></textarea>
                                        @error('form.komentar_auditee') 
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif

                                <!-- Komentar Auditor Field -->
                                @if($canFullAccess || $canCommentAuditor)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="komentar_auditor" class="form-label fw-medium">{{ __('Komentar Auditor') }}</label>
                                        <textarea class="form-control @error('form.komentar_auditor') is-invalid @enderror" 
                                                  rows="4"
                                                  placeholder="{{ __('Masukkan komentar auditor...') }}"
                                                  wire:model="form.komentar_auditor"></textarea>
                                        @error('form.komentar_auditor') 
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle me-2" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                            </svg>{{ __('Batal') }}
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="save">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy me-2" viewBox="0 0 16 16">
                                <path d="M11 2H9v3h2z"/>
                                <path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5m3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4zM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5z"/>
                            </svg>{{ __('Simpan') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle me-2" viewBox="0 0 16 16">
                                <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/>
                                <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                            </svg>
                            {{ __('Konfirmasi Hapus') }}
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Yakin ingin menghapus standar mutu ini? Tindakan ini tidak dapat dibatalkan.') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle me-2" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                            </svg>{{ __('Batal') }}
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash me-2" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1-.5-.5z"/>
                                <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0z"/>
                            </svg>{{ __('Hapus') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Detail Modal -->
        <div class="modal fade" id="viewModal" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye me-2" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                            </svg>
                            {{ __('Detail Indikator Sub Standar') }}
                        </h5>
                    </div>
                    <div class="modal-body pt-3">
                        @if($selectedStandar)
                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Tahun</label>
                                    <div class="fw-medium">{{ $selectedStandar->tahun->tahun }}</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Lembaga Akreditasi</label>
                                    <div class="fw-medium">{{ $selectedStandar->lembagaAkreditasi->lembaga }}</div>
                                </div>
                            </div>

                            <!-- Standar Nasional and Document Evidence -->
                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Standar Nasional</label>
                                    <div class="fw-medium">{{ $selectedStandar->standarNasional->standar }}</div>
                                    <small class="text-muted">
                                        {{ $selectedStandar->standarNasional->parent->parent->standar }} → 
                                        {{ $selectedStandar->standarNasional->parent->standar }}
                                    </small>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Bukti Dokumen</label>
                                    <div>
                                        @if($selectedStandar->bukti_dokumen)
                                            @if($selectedStandar->isBuktiDokumenLink())
                                                <a href="{{ $selectedStandar->bukti_dokumen }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                    Buka Google Drive
                                                </a>
                                            @elseif($selectedStandar->isBuktiDokumenFile())
                                                <a href="{{ asset('storage/' . $selectedStandar->bukti_dokumen) }}" target="_blank" class="btn btn-outline-success btn-sm">
                                                    Download PDF
                                                </a>
                                            @else
                                                <span class="text-muted">Format tidak valid</span>
                                            @endif
                                        @else
                                            <span class="text-muted">Belum ada bukti dokumen</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Status and Value -->
                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Status</label>
                                    <div>
                                        @if($selectedStandar->status == 'draft')
                                            <span class="badge bg-warning">Draft</span>
                                        @elseif($selectedStandar->status == 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @elseif($selectedStandar->status == 'nonaktif')
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1">Nilai Mutu</label>
                                    <div class="fw-medium">
                                        @if($selectedStandar->nilai_mutu)
                                            <span class="badge bg-label-primary">
                                                {{ number_format($selectedStandar->nilai_mutu, 2) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Comments -->
                            <div>
                                <label class="form-label text-muted mb-3">Komentar</label>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3">
                                            <div class="fw-medium mb-2">Auditee</div>
                                            <div class="text-muted">
                                                {{ $selectedStandar->komentar_auditee ?: 'Belum ada komentar' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3">
                                            <div class="fw-medium mb-2">Auditor</div>
                                            <div class="text-muted">
                                                {{ $selectedStandar->komentar_auditor ?: 'Belum ada komentar' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeViewModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle me-2" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                            </svg>{{ __('Tutup') }}
                        </button>                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Standar Mutu Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Daftar Standar Mutu') }}</h5>
                <div class="d-flex gap-2">
                    <!-- Search -->
                    <div class="position-relative">
                        <input type="text" 
                               class="form-control" 
                               placeholder="{{ __('Cari standar mutu...') }}"
                               wire:model.live.debounce.300ms="search"
                               style="width: 200px;">
                        <i class="bx bx-search position-absolute top-50 end-0 translate-middle-y me-3"></i>
                    </div>
                    <!-- Add Button -->
                    @if($canCreate)
                    <button class="btn btn-primary" wire:click="create">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle me-2" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                        </svg>{{ __('Tambah Standar Mutu') }}
                    </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>{{ __('NO') }}</th>
                                <th>{{ __('TAHUN') }}</th>
                                <th>{{ __('LEMBAGA AKREDITASI') }}</th>
                                <th>{{ __('STANDAR NASIONAL') }}</th>
                                <th>{{ __('STATUS') }}</th>
                                <th>{{ __('NILAI MUTU') }}</th>
                                <th>{{ __('BUKTI DOKUMEN') }}</th>
                                <th>{{ __('AKSI') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($standars as $index => $standar)
                                <tr>
                                    <td class="fw-medium">{{ $standars->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge bg-label-info">{{ $standar->tahun->tahun }}</span>
                                    </td>
                                    <td>{{ $standar->lembagaAkreditasi->lembaga }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $standar->standarNasional->standar }}</span>
                                            <small class="text-muted">
                                                {{ $standar->standarNasional->parent->parent->standar }} > 
                                                {{ $standar->standarNasional->parent->standar }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($standar->status == 'draft')
                                            <span class="badge bg-warning">{{ __('Draft') }}</span>
                                        @elseif($standar->status == 'aktif')
                                            <span class="badge bg-success">{{ __('Aktif') }}</span>
                                        @elseif($standar->status == 'nonaktif')
                                            <span class="badge bg-danger">{{ __('Nonaktif') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($standar->nilai_mutu)
                                            <span class="badge bg-label-primary">{{ number_format($standar->nilai_mutu, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($standar->bukti_dokumen)
                                            <span class="text-success fs-5">✔</span>
                                        @else
                                            <span class="text-danger fs-5">✖</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button class="btn btn-sm btn-success" wire:click="view({{ $standar->id }})" title="{{ __('View') }}">
                                                <i class="bx bx-show"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info" title="{{ __('Sub Standar') }}" onclick="window.location.href='{{ route('standar-mutu.sub-standar', $standar->id) }}'">
                                                <i class="bx bx-list-ul"></i>
                                            </button>
                                            @if($canEdit || $canUploadAndCommentAuditee || $canCommentAuditor)
                                            <button class="btn btn-sm btn-primary" wire:click="edit({{ $standar->id }})" title="{{ __('Edit') }}">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>
                                            @endif
                                            @if($canDelete)
                                            <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $standar->id }})" title="{{ __('Hapus') }}">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            @if($search)
                                                <p class="mb-0">{{ __('Data tidak ditemukan untuk pencarian "') }}{{ $search }}"</p>
                                                <small>{{ __('Coba ubah kata kunci pencarian') }}</small>
                                            @else
                                                <p class="mb-0">{{ __('Belum ada standar mutu yang ditambahkan') }}</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($standars->hasPages())
                    <div class="card-footer bg-white border-top-0 px-4 py-3">
                        <nav aria-label="Page navigation">
                            {{ $standars->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    // Modal events
    Livewire.on('show-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('standarMutuModal'));
        modal.show();
    });
    
    Livewire.on('hide-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('standarMutuModal'));
        if (modal) {
            modal.hide();
        }
    });

    Livewire.on('show-delete-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });

    Livewire.on('hide-delete-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        if (modal) {
            modal.hide();
        }
    });

    Livewire.on('show-view-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('viewModal'));
        modal.show();
    });

    Livewire.on('hide-view-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('viewModal'));
        if (modal) {
            modal.hide();
        }
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(dropdown => {
            const parentField = dropdown.closest('.mb-3');
            if (!parentField.contains(event.target)) {
                dropdown.classList.remove('show');
                // Emit event to Livewire to hide dropdown
                if (parentField.querySelector('input[wire\\:model\\.live="tahunSearch"]')) {
                    Livewire.emit('hideDropdown', 'tahun');
                } else if (parentField.querySelector('input[wire\\:model\\.live="lembagaSearch"]')) {
                    Livewire.emit('hideDropdown', 'lembaga');
                } else if (parentField.querySelector('input[wire\\:model\\.live="standarSearch"]')) {
                    Livewire.emit('hideDropdown', 'standar');
                }
            }
        });
    });
});
</script>