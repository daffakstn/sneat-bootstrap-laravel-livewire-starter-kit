<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Pegawai;
use App\Models\Jabatan;
use App\Models\Prodi;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public $showModal = false;
    public $showDeleteModal = false;
    public $isEditMode = false;
    public $userId = null;
    public $userToDelete = null;
    public $search = ''; // General search for the table
    public $jabatanSearch = ''; // Search for jabatan dropdown
    public $prodiSearch = ''; // Search for prodi dropdown
    public $showJabatanDropdown = false; // Dropdown visibility
    public $showProdiDropdown = false; // Dropdown visibility

    public $form = [
        'username' => '',
        'password' => '',
        'status_akses' => 'aktif',
        'nidn_nuptk' => '',
        'nama' => '',
        'email' => '',
        'roles' => ['Admin'], // Default to Admin for new users
        'jabatan_id' => '',
        'prodi_id' => '',
    ];

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

    public function updatedJabatanSearch()
    {
        $this->showJabatanDropdown = !empty($this->jabatanSearch);
    }

    public function updatedProdiSearch()
    {
        $this->showProdiDropdown = !empty($this->prodiSearch);
    }

    public function hideDropdown($type)
    {
        switch ($type) {
            case 'jabatan':
                $this->showJabatanDropdown = false;
                break;
            case 'prodi':
                $this->showProdiDropdown = false;
                break;
        }
    }

    public function selectJabatan($id, $jabatan, $unit)
    {
        $this->form['jabatan_id'] = $id;
        $this->jabatanSearch = "$jabatan ($unit)";
        $this->showJabatanDropdown = false;
    }

    public function selectProdi($id, $prodi)
    {
        $this->form['prodi_id'] = $id;
        $this->prodiSearch = $prodi;
        $this->showProdiDropdown = false;
    }

    public function getFilteredJabatansProperty()
    {
        return Jabatan::when($this->jabatanSearch, function ($query) {
            $query->where('jabatan', 'like', '%' . $this->jabatanSearch . '%')
                  ->orWhere('unit', 'like', '%' . $this->jabatanSearch . '%');
        })->limit(10)->get();
    }

    public function getFilteredProdisProperty()
    {
        return Prodi::when($this->prodiSearch, function ($query) {
            $query->where('nama_prodi', 'like', '%' . $this->prodiSearch . '%');
        })->limit(10)->get();
    }

    public function resetForm()
    {
        $this->resetErrorBag();
        $this->form = [
            'username' => '',
            'password' => '',
            'status_akses' => 'aktif',
            'nidn_nuptk' => '',
            'nama' => '',
            'email' => '',
            'roles' => ['Admin'],
            'jabatan_id' => '',
            'prodi_id' => '',
        ];
        $this->jabatanSearch = '';
        $this->prodiSearch = '';
        $this->showJabatanDropdown = false;
        $this->showProdiDropdown = false;
        $this->isEditMode = false;
        $this->userId = null;
        $this->showModal = false;
        $this->showDeleteModal = false;
        $this->userToDelete = null;
        $this->search = '';
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
        $user = User::with(['pegawai', 'roles'])->findOrFail($id);
        $this->form = [
            'username' => $user->username,
            'password' => '',
            'status_akses' => $user->status_akses,
            'nidn_nuptk' => $user->pegawai->nidn_nuptk ?? '',
            'nama' => $user->pegawai->nama ?? '',
            'email' => $user->pegawai->email ?? $user->email ?? '',
            'roles' => $user->roles->pluck('name')->toArray(),
            'jabatan_id' => $user->pegawai->jabatan_id ?? '',
            'prodi_id' => $user->pegawai->prodi_id ?? '',
        ];
        $this->jabatanSearch = $user->pegawai && $user->pegawai->jabatan ? 
            "{$user->pegawai->jabatan->jabatan} ({$user->pegawai->jabatan->unit})" : '';
        $this->prodiSearch = $user->pegawai && $user->pegawai->prodi ? 
            $user->pegawai->prodi->nama_prodi : '';
        $this->userId = $id;
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
        $this->userToDelete = $id;
        $this->showDeleteModal = true;
        $this->dispatch('show-delete-modal');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->userToDelete = null;
        $this->dispatch('hide-delete-modal');
    }

    public function save()
    {
        if (is_bool($this->form['status_akses'])) {
            $this->form['status_akses'] = $this->form['status_akses'] ? 'aktif' : 'nonaktif';
        }

        $validated = $this->validate([
            'form.username' => ['required', 'string', 'max:255', 'unique:users,username,' . ($this->userId ?: 'NULL')],
            'form.password' => [$this->isEditMode ? 'nullable' : 'required', 'string', 'min:8'],
            'form.status_akses' => ['required', 'in:aktif,nonaktif'],
            'form.nidn_nuptk' => ['required', 'string', 'max:255', 'unique:pegawai,nidn_nuptk,' . ($this->userId ? optional(Pegawai::where('user_id', $this->userId)->first())->id : 'NULL')],
            'form.nama' => ['required', 'string', 'max:255'],
            'form.email' => ['required', 'email', 'max:255', 'unique:pegawai,email,' . ($this->userId ? optional(Pegawai::where('user_id', $this->userId)->first())->id : 'NULL')],
            'form.roles' => ['required', 'array', 'min:1'],
            'form.roles.*' => ['in:Admin,Auditee,Auditor,Pimpinan'],
            'form.jabatan_id' => ['required', 'exists:jabatan,id'],
            'form.prodi_id' => ['nullable', 'exists:prodi,id'],
        ], [
            'form.username.required' => 'Username harus diisi.',
            'form.username.unique' => 'Username sudah digunakan.',
            'form.password.required' => 'Password harus diisi.',
            'form.password.min' => 'Password minimal 8 karakter.',
            'form.status_akses.required' => 'Status akses harus dipilih.',
            'form.nidn_nuptk.required' => 'NIDN/NUPTK harus diisi.',
            'form.nidn_nuptk.unique' => 'NIDN/NUPTK sudah digunakan.',
            'form.nama.required' => 'Nama harus diisi.',
            'form.email.required' => 'Email harus diisi.',
            'form.email.email' => 'Email harus valid.',
            'form.email.unique' => 'Email sudah digunakan.',
            'form.roles.required' => 'Setidaknya satu role harus dipilih.',
            'form.jabatan_id.required' => 'Jabatan harus dipilih.',
            'form.jabatan_id.exists' => 'Jabatan yang dipilih tidak valid.',
            'form.prodi_id.exists' => 'Program studi yang dipilih tidak valid.',
        ]);

        try {
            if ($this->isEditMode) {
                $user = User::findOrFail($this->userId);
                $user->update([
                    'username' => $validated['form']['username'],
                    'email' => $validated['form']['email'],
                    'status_akses' => $validated['form']['status_akses'],
                    'password' => $validated['form']['password'] ? Hash::make($validated['form']['password']) : $user->password,
                ]);

                if ($user->pegawai) {
                    $user->pegawai->update([
                        'nidn_nuptk' => $validated['form']['nidn_nuptk'],
                        'nama' => $validated['form']['nama'],
                        'email' => $validated['form']['email'],
                        'jabatan_id' => $validated['form']['jabatan_id'],
                        'prodi_id' => $validated['form']['prodi_id'] ?: null,
                    ]);
                } else {
                    Pegawai::create([
                        'nidn_nuptk' => $validated['form']['nidn_nuptk'],
                        'nama' => $validated['form']['nama'],
                        'email' => $validated['form']['email'],
                        'jabatan_id' => $validated['form']['jabatan_id'],
                        'prodi_id' => $validated['form']['prodi_id'] ?: null,
                        'user_id' => $user->id,
                    ]);
                }

                $user->roles()->sync(Role::whereIn('name', $validated['form']['roles'])->pluck('id'));

                session()->flash('success', 'User berhasil diperbarui!');
            } else {
                $user = User::create([
                    'username' => $validated['form']['username'],
                    'email' => $validated['form']['email'],
                    'password' => Hash::make($validated['form']['password']),
                    'status_akses' => $validated['form']['status_akses'],
                ]);

                Pegawai::create([
                    'nidn_nuptk' => $validated['form']['nidn_nuptk'],
                    'nama' => $validated['form']['nama'],
                    'email' => $validated['form']['email'],
                    'jabatan_id' => $validated['form']['jabatan_id'],
                    'prodi_id' => $validated['form']['prodi_id'] ?: null,
                    'user_id' => $user->id,
                ]);

                $user->roles()->attach(Role::whereIn('name', $validated['form']['roles'])->pluck('id'));

                session()->flash('success', 'User berhasil ditambahkan!');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $user = User::findOrFail($this->userToDelete);

            if ($user->pegawai) {
                $user->pegawai->delete();
            }

            $user->roles()->detach();
            $user->delete();
            session()->flash('success', 'User berhasil dihapus!');
            $this->closeDeleteModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus user: ' . $e->getMessage());
            $this->closeDeleteModal();
        }
    }

    public function render()
    {
        $users = User::with(['pegawai.jabatan', 'pegawai.prodi', 'roles'])
            ->when($this->search, function ($query) {
                $query->where('username', 'like', '%' . $this->search . '%')
                      ->orWhereHas('pegawai', function ($q) {
                          $q->where('nama', 'like', '%' . $this->search . '%')
                            ->orWhere('nidn_nuptk', 'like', '%' . $this->search . '%')
                            ->orWhereHas('jabatan', function ($qq) {
                                $qq->where('jabatan', 'like', '%' . $this->search . '%')
                                   ->orWhere('unit', 'like', '%' . $this->search . '%');
                            })
                            ->orWhereHas('prodi', function ($qq) {
                                $qq->where('nama_prodi', 'like', '%' . $this->search . '%');
                            });
                      })
                      ->orWhereHas('roles', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhere('status_akses', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.users.index', [
            'users' => $users,
            'jabatans' => Jabatan::orderBy('jabatan')->get(),
            'prodis' => Prodi::orderBy('nama_prodi')->get(),
            'roles' => Role::all(),
            'filteredJabatans' => $this->filteredJabatans,
            'filteredProdis' => $this->filteredProdis,
            'isEditMode' => $this->isEditMode,
            'showModal' => $this->showModal,
            'showDeleteModal' => $this->showDeleteModal,
            'form' => $this->form,
        ])->layout('components.layouts.app', ['title' => __('Manajemen Pengguna')]);
    }

    public function updatedFormStatusAkses()
    {
        $this->form['status_akses'] = $this->form['status_akses'] ? 'aktif' : 'nonaktif';
    }
}