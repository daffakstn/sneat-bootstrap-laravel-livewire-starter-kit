<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\Prodi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed all 8 prodi
        $prodis = [
            'Teknologi Rekayasa Perangkat Lunak',
            'Teknologi Rekayasa Pemeliharaan Alat Berat',
            'Teknologi Rekayasa Logistik',
            'Teknik Mesin',
            'Teknik Elektronika',
            'Teknik Listrik',
            'Bisnis Digital',
            'Akuntansi Perpajakan',
        ];

        foreach ($prodis as $namaProdi) {
            Prodi::create(['nama_prodi' => $namaProdi]);
        }

        // Seed jabatan
        $jabatan1 = Jabatan::create([
            'jabatan' => 'Kabag IT',
            'unit' => 'Wadir 1',
        ]);

        $jabatan2 = Jabatan::create([
            'jabatan' => 'Staff IT',
            'unit' => 'Wadir 1',
        ]);

        // Seed users (tanpa prodi)
        $users = [
            [
                'name' => 'Admin User',
                'username' => 'admin_user',
                'email' => 'admin@example.com',
                'role' => 'Admin',
                'nidn_nuptk' => 'NIDN0001',
                'jabatan_id' => $jabatan1->id,
            ],
            [
                'name' => 'Auditee User',
                'username' => 'auditee_user',
                'email' => 'auditee@example.com',
                'role' => 'Auditee',
                'nidn_nuptk' => 'NIDN0002',
                'jabatan_id' => $jabatan2->id,
            ],
            [
                'name' => 'Auditor User',
                'username' => 'auditor_user',
                'email' => 'auditor@example.com',
                'role' => 'Auditor',
                'nidn_nuptk' => 'NIDN0003',
                'jabatan_id' => $jabatan1->id,
            ],
            [
                'name' => 'Pimpinan User',
                'username' => 'pimpinan_user',
                'email' => 'pimpinan@example.com',
                'role' => 'Pimpinan',
                'nidn_nuptk' => 'NIDN0004',
                'jabatan_id' => $jabatan2->id,
            ],
        ];

        foreach ($users as $userData) {
            // Create user
            $user = User::create([
                'username' => $userData['username'],
                'email' => $userData['email'],
                'password' => Hash::make('1'),
                'status_akses' => 'aktif',
            ]);

            // Create pegawai without prodi_id (set to null)
            Pegawai::create([
                'nidn_nuptk' => $userData['nidn_nuptk'],
                'nama' => $userData['name'],
                'email' => $userData['email'],
                'jabatan_id' => $userData['jabatan_id'],
                'user_id' => $user->id,
                'prodi_id' => null, // explicitly null
            ]);

            // Attach single role
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }
        }
    }
}
