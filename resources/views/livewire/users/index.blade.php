@section('title', __('Manajemen Pengguna'))

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

        <!-- Modal Form -->
        <div class="modal fade" id="userModal" tabindex="-1" wire:ignore.self>
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
                            {{ $isEditMode ? __('Edit Pengguna') : __('Tambah Pengguna') }}
                        </h5>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row g-3">
                                <!-- Row 1: Username & Password -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label fw-medium">{{ __('Username') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('form.username') is-invalid @enderror" 
                                               id="username" wire:model="form.username" placeholder="{{ __('Masukkan username') }}">
                                        @error('form.username') 
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-medium">
                                            {{ __('Password') }} <span class="text-danger">*</span>
                                            @if($isEditMode) 
                                                <small class="text-muted">({{ __('Kosongkan jika tidak ingin mengubah') }})</small> 
                                            @endif
                                        </label>
                                        <input type="password" class="form-control @error('form.password') is-invalid @enderror" 
                                               id="password" wire:model="form.password" placeholder="{{ __('Masukkan password') }}">
                                        @error('form.password') 
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Row 2: Nama & NIDN/NUPTK -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama" class="form-label fw-medium">{{ __('Nama') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('form.nama') is-invalid @enderror" 
                                               id="nama" wire:model="form.nama" placeholder="{{ __('Masukkan nama lengkap') }}">
                                        @error('form.nama') 
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nidn_nuptk" class="form-label fw-medium">{{ __('NIDN/NUPTK') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('form.nidn_nuptk') is-invalid @enderror" 
                                               id="nidn_nuptk" wire:model="form.nidn_nuptk" placeholder="{{ __('Masukkan NIDN/NUPTK') }}">
                                        @error('form.nidn_nuptk') 
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Row 3: Email & Status Akses -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-medium">{{ __('Email') }} <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('form.email') is-invalid @enderror" 
                                               id="email" wire:model="form.email" placeholder="{{ __('Masukkan email') }}">
                                        @error('form.email') 
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status_akses" class="form-label fw-medium">{{ __('Status Akses') }} <span class="text-danger">*</span></label>
                                        <div class="dropdown w-100">
                                            <button class="btn btn-outline-secondary dropdown-toggle w-100 d-flex justify-content-between align-items-center @error('form.status_akses') is-invalid @enderror" 
                                                    type="button" 
                                                    data-bs-toggle="dropdown" 
                                                    aria-expanded="false">
                                                <span class="text-start">
                                                    @if($form['status_akses'])
                                                        @if($form['status_akses'] == 'aktif')
                                                            {{ __('Aktif') }}
                                                        @elseif($form['status_akses'] == 'nonaktif')
                                                            {{ __('Nonaktif') }}
                                                        @endif
                                                    @else
                                                        {{ __('Pilih Status Akses') }}
                                                    @endif
                                                </span>
                                            </button>
                                            <ul class="dropdown-menu w-100">
                                                <li><a class="dropdown-item" href="javascript:void(0);" wire:click="$set('form.status_akses', '')">{{ __('Pilih Status Akses') }}</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);" wire:click="$set('form.status_akses', 'aktif')">{{ __('Aktif') }}</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);" wire:click="$set('form.status_akses', 'nonaktif')">{{ __('Nonaktif') }}</a></li>
                                            </ul>
                                        </div>
                                        @error('form.status_akses') 
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Row 4: Jabatan & Program Studi -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="jabatan_id" class="form-label fw-medium">{{ __('Jabatan') }} <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" 
                                                   class="form-control @error('form.jabatan_id') is-invalid @enderror" 
                                                   placeholder="{{ __('Cari dan pilih jabatan...') }}"
                                                   wire:model.live="jabatanSearch"
                                                   wire:focus="$set('showJabatanDropdown', true)"
                                                   autocomplete="off">
                                            <i class="bx bx-search position-absolute top-50 end-0 translate-middle-y me-3"></i>
                                            
                                            @if($showJabatanDropdown && count($filteredJabatans) > 0)
                                                <div class="dropdown-menu show w-100 mt-1" style="max-height: 200px; overflow-y: auto;">
                                                    @foreach($filteredJabatans as $jabatan)
                                                        <a class="dropdown-item" 
                                                           href="javascript:void(0);" 
                                                           wire:click="selectJabatan({{ $jabatan->id }}, '{{ $jabatan->jabatan }}', '{{ $jabatan->unit }}')">
                                                            {{ $jabatan->jabatan }} ({{ $jabatan->unit }})
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        @error('form.jabatan_id') 
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="prodi_id" class="form-label fw-medium">{{ __('Program Studi') }}</label>
                                        <div class="position-relative">
                                            <input type="text" 
                                                   class="form-control @error('form.prodi_id') is-invalid @enderror" 
                                                   placeholder="{{ __('Cari dan pilih program studi...') }}"
                                                   wire:model.live="prodiSearch"
                                                   wire:focus="$set('showProdiDropdown', true)"
                                                   autocomplete="off">
                                            <i class="bx bx-search position-absolute top-50 end-0 translate-middle-y me-3"></i>
                                            
                                            @if($showProdiDropdown && count($filteredProdis) > 0)
                                                <div class="dropdown-menu show w-100 mt-1" style="max-height: 200px; overflow-y: auto;">
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
                                        @error('form.prodi_id') 
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Row 5: Roles -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">{{ __('Roles') }} <span class="text-danger">*</span></label>
                                        <div class="mt-2">
                                            <div class="row g-2">
                                                @foreach($roles as $role)
                                                    <div class="col-md-6">
                                                        <div class="form-check form-switch mb-2">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   id="role{{ $role->name }}" 
                                                                   wire:model="form.roles" 
                                                                   value="{{ $role->name }}">
                                                            <label class="form-check-label" for="role{{ $role->name }}">
                                                                <span class="badge 
                                                                    @if($role->name == 'Admin') bg-label-primary
                                                                    @elseif($role->name == 'Auditee') bg-label-info
                                                                    @elseif($role->name == 'Auditor') bg-label-warning
                                                                    @elseif($role->name == 'Pimpinan') bg-label-success
                                                                    @else bg-label-secondary
                                                                    @endif me-1">
                                                                    {{ $role->name }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @error('form.roles') 
                                            <div class="text-danger small mt-1">{{ $message }}</div>
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
                            {{ __('Konfirmasi Hapus Pengguna') }}
                        </h5>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.') }}</p>
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

        <!-- User Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('Daftar Pengguna') }}</h5>
                <div class="d-flex gap-2">
                    <!-- Search -->
                    <div class="position-relative">
                        <input type="text" 
                               class="form-control" 
                               placeholder="{{ __('Cari pengguna...') }}"
                               wire:model.live.debounce.300ms="search"
                               style="width: 200px;">
                        <i class="bx bx-search position-absolute top-50 end-0 translate-middle-y me-3"></i>
                    </div>
                    <!-- Add Button -->
                    <button class="btn btn-primary" wire:click="create">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle me-2" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                        </svg>{{ __('Tambah Pengguna') }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>{{ __('NO') }}</th>
                                <th>{{ __('NAMA LENGKAP') }}</th>
                                <th>{{ __('NIDN/NUPTK') }}</th>
                                <th>{{ __('JABATAN') }}</th>
                                <th>{{ __('UNIT') }}</th>
                                <th>{{ __('ROLES') }}</th>
                                <th>{{ __('STATUS AKSES') }}</th>
                                <th>{{ __('AKSI') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $index => $user)
                                <tr>
                                    <td class="fw-medium">{{ $users->firstItem() + $index }}</td>
                                    <td>{{ $user->pegawai->nama ?? '-' }}</td>
                                    <td>{{ $user->pegawai->nidn_nuptk ?? '-' }}</td>
                                    <td>{{ $user->pegawai->jabatan->jabatan ?? '-' }}</td>
                                    <td>{{ $user->pegawai->jabatan->unit ?? '-' }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge 
                                                @if($role->name == 'Admin') bg-label-primary
                                                @elseif($role->name == 'Auditee') bg-label-info
                                                @elseif($role->name == 'Auditor') bg-label-warning
                                                @elseif($role->name == 'Pimpinan') bg-label-success
                                                @else bg-label-secondary
                                                @endif
                                            ">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                        @if($user->roles->isEmpty())
                                            <span class="badge bg-label-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $user->status_akses === 'aktif' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $user->status_akses === 'aktif' ? __('Aktif') : __('Nonaktif') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button class="btn btn-sm btn-primary" wire:click="edit({{ $user->id }})" title="{{ __('Edit') }}">
                                                <i class="bx bx-edit-alt"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $user->id }})" title="{{ __('Hapus') }}">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <div class="text-muted">
                                            @if($search)
                                                <p class="mb-0">{{ __('Data tidak ditemukan untuk pencarian "') }}{{ $search }}"</p>
                                                <small>{{ __('Coba ubah kata kunci pencarian') }}</small>
                                            @else
                                                <p class="mb-0">{{ __('Belum ada pengguna yang ditambahkan') }}</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="card-footer bg-white border-top-0 px-4 py-3">
                        <nav aria-label="Page navigation">
                            {{ $users->links('pagination::bootstrap-5') }}
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
        const modal = new bootstrap.Modal(document.getElementById('userModal'));
        modal.show();
    });
    
    Livewire.on('hide-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
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

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(dropdown => {
            if (!dropdown.closest('.position-relative').contains(event.target)) {
                dropdown.classList.remove('show');
                const parentField = dropdown.closest('.mb-3');
                if (parentField.querySelector('input[wire\\:model\\.live="jabatanSearch"]')) {
                    Livewire.emit('hideDropdown', 'jabatan');
                } else if (parentField.querySelector('input[wire\\:model\\.live="prodiSearch"]')) {
                    Livewire.emit('hideDropdown', 'prodi');
                }
            }
        });
    });
});
</script>