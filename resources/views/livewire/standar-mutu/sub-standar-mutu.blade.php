@section('title', __('Sub Standar Mutu'))

<div>
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
                    <strong>{{ __('Informasi:') }}</strong> {{ __('Anda hanya memiliki akses untuk melihat data sub standar.') }}
                </div>
            @endif

            <!-- Modal Form -->
            <div class="modal fade" id="subStandarModal" tabindex="-1" wire:ignore.self>
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
                                {{ $isEditMode ? __('Edit Sub Standar') : __('Tambah Sub Standar') }}
                            </h5>
                        </div>
                        <div class="modal-body">
                            @if($modalMessage)
                                <div class="alert alert-{{ $modalMessageType === 'error' ? 'danger' : 'success' }} alert-dismissible fade show" role="alert">
                                    @if($modalMessageType === 'error')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle me-2" viewBox="0 0 16 16">
                                            <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/>
                                            <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle me-2" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                        </svg>
                                    @endif
                                    {{ $modalMessage }}
                                </div>
                            @endif

                            <form wire:submit.prevent="save">
                                <div class="row g-3">
                                    <!-- Program Studi Field -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="prodi_id" class="form-label fw-medium">{{ __('Program Studi') }} <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <input type="text" 
                                                       class="form-control @error('selectedProdis') is-invalid @enderror" 
                                                       placeholder="{{ __('Cari dan pilih program studi...') }}"
                                                       wire:model.live="prodiSearch"
                                                       wire:focus="$set('showProdiDropdown', true)"
                                                       autocomplete="off">
                                                <i class="bx bx-search position-absolute top-50 end-0 translate-middle-y me-3"></i>
                                                
                                                @if($showProdiDropdown && count($filteredProdis) > 0)
                                                    <div class="dropdown-menu show w-100 mt-1" style="max-height: 200px; overflow-y: auto;">
                                                        <button type="button" class="dropdown-item text-primary" wire:click="selectAllProdi">
                                                            {{ __('Pilih Semua Program Studi') }}
                                                        </button>
                                                        <div class="dropdown-divider"></div>
                                                        @foreach($filteredProdis as $prodi)
                                                            <a class="dropdown-item" 
                                                               href="javascript:void(0);" 
                                                               wire:click="selectProdi({{ $prodi->id }}, '{{ $prodi->nama_prodi }}')">
                                                                {{ $prodi->nama_prodi }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Selected Prodis Display -->
                                            @if(count($selectedProdis) > 0)
                                                <div class="mt-2">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach($selectedProdiNames as $index => $prodiName)
                                                            <span class="badge bg-primary d-flex align-items-center gap-1">
                                                                {{ $prodiName }}
                                                                <button type="button" 
                                                                        class="btn-close btn-close-white" 
                                                                        wire:click="removeProdi({{ $index }})"
                                                                        title="{{ __('Hapus') }}">
                                                                </button>
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                    <small class="text-muted">{{ __('Klik X untuk menghapus program studi') }}</small>
                                                </div>
                                            @endif
                                            
                                            @error('selectedProdis') 
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="sub_standar" class="form-label fw-medium">{{ __('Sub Standar') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('form.sub_standar') is-invalid @enderror" 
                                                   id="sub_standar" wire:model="form.sub_standar" placeholder="{{ __('Masukkan nama sub standar') }}">
                                            @error('form.sub_standar') 
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="target" class="form-label fw-medium">{{ __('Target') }}</label>
                                            <textarea class="form-control @error('form.target') is-invalid @enderror" 
                                                      id="target" wire:model="form.target" placeholder="{{ __('Masukkan target (opsional)') }}" rows="3"></textarea>
                                            @error('form.target') 
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="indikator_0" class="form-label fw-medium">{{ __('Indikator Level 0 (Kondisi Terburuk)') }} <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('form.indikator_0') is-invalid @enderror" 
                                                      id="indikator_0" wire:model="form.indikator_0" placeholder="{{ __('Masukkan indikator level 0') }}" rows="3"></textarea>
                                            @error('form.indikator_0') 
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="indikator_1" class="form-label fw-medium">{{ __('Indikator Level 1 (Kondisi Minimal)') }} <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('form.indikator_1') is-invalid @enderror" 
                                                      id="indikator_1" wire:model="form.indikator_1" placeholder="{{ __('Masukkan indikator level 1') }}" rows="3"></textarea>
                                            @error('form.indikator_1') 
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="indikator_2" class="form-label fw-medium">{{ __('Indikator Level 2 (Kondisi Cukup)') }} <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('form.indikator_2') is-invalid @enderror" 
                                                      id="indikator_2" wire:model="form.indikator_2" placeholder="{{ __('Masukkan indikator level 2') }}" rows="3"></textarea>
                                            @error('form.indikator_2') 
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="indikator_3" class="form-label fw-medium">{{ __('Indikator Level 3 (Kondisi Baik)') }} <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('form.indikator_3') is-invalid @enderror" 
                                                      id="indikator_3" wire:model="form.indikator_3" placeholder="{{ __('Masukkan indikator level 3') }}" rows="3"></textarea>
                                            @error('form.indikator_3') 
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="indikator_4" class="form-label fw-medium">{{ __('Indikator Level 4 (Kondisi Ideal)') }} <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('form.indikator_4') is-invalid @enderror" 
                                                      id="indikator_4" wire:model="form.indikator_4" placeholder="{{ __('Masukkan indikator level 4') }}" rows="3"></textarea>
                                            @error('form.indikator_4') 
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
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
                                {{ __('Konfirmasi Hapus Sub Standar') }}
                            </h5>
                        </div>
                        <div class="modal-body">
                            <p>{{ __('Yakin ingin menghapus sub standar ini? Tindakan ini tidak dapat dibatalkan.') }}</p>
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

            <!-- View Indikator Modal -->
            <div class="modal fade" id="viewIndikatorModal" tabindex="-1" wire:ignore.self>
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
                        <div class="modal-body">
                            @if($indikatorViewData)
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="view_indikator_0" class="form-label fw-medium">{{ __('Indikator Level 0 (Kondisi Terburuk)') }}</label>
                                            <textarea class="form-control" id="view_indikator_0" rows="3" disabled readonly>{{ $indikatorViewData['indikator_0'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="view_indikator_1" class="form-label fw-medium">{{ __('Indikator Level 1 (Kondisi Minimal)') }}</label>
                                            <textarea class="form-control" id="view_indikator_1" rows="3" disabled readonly>{{ $indikatorViewData['indikator_1'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="view_indikator_2" class="form-label fw-medium">{{ __('Indikator Level 2 (Kondisi Cukup)') }}</label>
                                            <textarea class="form-control" id="view_indikator_2" rows="3" disabled readonly>{{ $indikatorViewData['indikator_2'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="view_indikator_3" class="form-label fw-medium">{{ __('Indikator Level 3 (Kondisi Baik)') }}</label>
                                            <textarea class="form-control" id="view_indikator_3" rows="3" disabled readonly>{{ $indikatorViewData['indikator_3'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="view_indikator_4" class="form-label fw-medium">{{ __('Indikator Level 4 (Kondisi Ideal)') }}</label>
                                            <textarea class="form-control" id="view_indikator_4" rows="3" disabled readonly>{{ $indikatorViewData['indikator_4'] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeViewIndikatorModal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle me-2" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>{{ __('Tutup') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sub Standar Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Sub Standar untuk Standar Mutu : ') }} {{ $standar->tahun->tahun }} {{ $standar->lembagaAkreditasi->lembaga }} {{ $standar->standarNasional->standar }}</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('standar-mutu') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                            </svg>{{ __('Kembali') }}
                        </a>
                        <!-- Search -->
                        <div class="position-relative">
                            <input type="text" 
                                   class="form-control" 
                                   placeholder="{{ __('Cari sub standar...') }}"
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
                            </svg>{{ __('Tambah Sub Standar') }}
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
                                    <th>{{ __('PROGRAM STUDI') }}</th>
                                    <th>{{ __('SUB STANDAR') }}</th>
                                    <th>{{ __('TARGET') }}</th>
                                    <th>{{ __('AKSI') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subStandars as $index => $subStandar)
                                    <tr>
                                        <td class="fw-medium">{{ $subStandars->firstItem() + $index }}</td>
                                        <td>{{ $subStandar->prodi->nama_prodi }}</td>
                                        <td>{{ $subStandar->sub_standar }}</td>
                                        <td>
                                            @if($subStandar->target)
                                                <span class="text-muted">{{ Str::limit($subStandar->target, 50) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-center">
                                                <button class="btn btn-sm btn-success" wire:click="showIndikator({{ $subStandar->id }})" title="{{ __('Lihat Indikator') }}">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                @if($canEdit)
                                                <button class="btn btn-sm btn-primary" wire:click="edit({{ $subStandar->id }})" title="{{ __('Edit') }}">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                                @endif
                                                @if($canDelete)
                                                <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $subStandar->id }})" title="{{ __('Hapus') }}">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                @if($search)
                                                    <p class="mb-0">{{ __('Data tidak ditemukan untuk pencarian "') }}{{ $search }}"</p>
                                                    <small>{{ __('Coba ubah kata kunci pencarian') }}</small>
                                                @else
                                                    <p class="mb-0">{{ __('Belum ada sub standar yang ditambahkan') }}</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($subStandars->hasPages())
                        <div class="card-footer bg-white border-top-0 px-4 py-3">
                            <nav aria-label="Page navigation">
                                {{ $subStandars->links('pagination::bootstrap-5') }}
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('show-modal', () => {
            const modal = new bootstrap.Modal(document.getElementById('subStandarModal'));
            modal.show();
        });
        Livewire.on('hide-modal', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('subStandarModal'));
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
        Livewire.on('show-view-indikator-modal', () => {
            const modal = new bootstrap.Modal(document.getElementById('viewIndikatorModal'));
            modal.show();
        });
        Livewire.on('hide-view-indikator-modal', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('viewIndikatorModal'));
            if (modal) {
                modal.hide();
            }
        });
    });
    </script>
</div>