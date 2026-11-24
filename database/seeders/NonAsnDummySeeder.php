<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Employee;
use App\Models\Administration;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class NonAsnDummySeeder extends Seeder
{
    public function run()
    {
        // 1. Pastikan 3 Role Utama Non-ASN Tersedia
        $rolesData = [
            'Satpam' => Role::firstOrCreate(['name' => 'Satpam']),
            'Driver' => Role::firstOrCreate(['name' => 'Driver']),
            'Petugas Kebersihan' => Role::firstOrCreate(['name' => 'Petugas Kebersihan']),
        ];

        // 2. Daftar 10 User Dummy Non-ASN
        $dummyUsers = [
            // 4 User Satpam
            [
                'name' => 'Joko Susilo',
                'email' => 'joko.satpam@nonasn.id',
                'role_name' => 'Satpam',
                'nik' => '3201010101900001',
                'phone' => '081234567801',
                'address' => 'Jl. Keamanan No. 1, Jakarta',
            ],
            [
                'name' => 'Bambang Prasetyo',
                'email' => 'bambang.satpam@nonasn.id',
                'role_name' => 'Satpam',
                'nik' => '3201010101900002',
                'phone' => '081234567802',
                'address' => 'Jl. Gardu No. 12, Jakarta',
            ],
            [
                'name' => 'Hendra Gunawan',
                'email' => 'hendra.satpam@nonasn.id',
                'role_name' => 'Satpam',
                'nik' => '3201010101900003',
                'phone' => '081234567803',
                'address' => 'Jl. Pos Ronda No. 5, Jakarta',
            ],
            [
                'name' => 'Supriyadi',
                'email' => 'supriyadi.satpam@nonasn.id',
                'role_name' => 'Satpam',
                'nik' => '3201010101900004',
                'phone' => '081234567804',
                'address' => 'Jl. Perintis Kemerdekaan No. 8, Jakarta',
            ],

            // 3 User Driver
            [
                'name' => 'Agus Santoso',
                'email' => 'agus.driver@nonasn.id',
                'role_name' => 'Driver',
                'nik' => '3201010101900005',
                'phone' => '081234567805',
                'address' => 'Jl. Armada No. 15, Jakarta',
            ],
            [
                'name' => 'Budi Hartono',
                'email' => 'budi.driver@nonasn.id',
                'role_name' => 'Driver',
                'nik' => '3201010101900006',
                'phone' => '081234567806',
                'address' => 'Jl. Otista Raya No. 44, Jakarta',
            ],
            [
                'name' => 'Slamet Riyadi',
                'email' => 'slamet.driver@nonasn.id',
                'role_name' => 'Driver',
                'nik' => '3201010101900007',
                'phone' => '081234567807',
                'address' => 'Jl. Garasi Dinas No. 20, Jakarta',
            ],

            // 3 User Petugas Kebersihan
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.kebersihan@nonasn.id',
                'role_name' => 'Petugas Kebersihan',
                'nik' => '3201010101900008',
                'phone' => '081234567808',
                'address' => 'Jl. Asri Indah No. 3, Jakarta',
            ],
            [
                'name' => 'Sri Wahyuni',
                'email' => 'sri.kebersihan@nonasn.id',
                'role_name' => 'Petugas Kebersihan',
                'nik' => '3201010101900009',
                'phone' => '081234567809',
                'address' => 'Jl. Melati Putih No. 7, Jakarta',
            ],
            [
                'name' => 'Yanto Wibowo',
                'email' => 'yanto.kebersihan@nonasn.id',
                'role_name' => 'Petugas Kebersihan',
                'nik' => '3201010101900010',
                'phone' => '081234567810',
                'address' => 'Jl. Cempaka Hijau No. 11, Jakarta',
            ],
        ];

        foreach ($dummyUsers as $uData) {
            $user = User::firstOrCreate(
                ['email' => $uData['email']],
                [
                    'name' => $uData['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                ]
            );

            $role = $rolesData[$uData['role_name']];

            Employee::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role_id' => $role->id,
                    'work_email' => $uData['email'],
                    'nik' => $uData['nik'],
                    'phone_number' => $uData['phone'],
                    'address' => $uData['address'],
                    'status' => 'Kontrak',
                    'status_employee' => 'Ready',
                    'join_date' => Carbon::now()->subMonths(6)->toDateString(),
                    'education' => 'SMA / SMK Sederajat',
                ]
            );
        }

        // 3. Tambahkan contoh data izin dengan bukti surat sakit untuk sampel
        $firstUser = User::where('email', 'joko.satpam@nonasn.id')->first();
        if ($firstUser) {
            Administration::firstOrCreate(
                [
                    'user_id' => $firstUser->id,
                    'leave_category' => 'cuti_sakit',
                    'start_date' => Carbon::now()->subDays(2)->toDateString(),
                ],
                [
                    'end_date' => Carbon::now()->toDateString(),
                    'lama_hari_cuti' => '3',
                    'description' => 'Izin tidak dapat bertugas dinas jaga keamanan karena demam tinggi dan flu berat. Surat keterangan dokter dari RSUD terlampir.',
                    'bring_laptop' => false,
                    'contacted' => true,
                    'status' => 'approved',
                    'attachment' => null,
                    'approver_note' => 'Disetujui. Semoga lekas sembuh dan dapat bertugas kembali.',
                ]
            );
        }

        $secondUser = User::where('email', 'agus.driver@nonasn.id')->first();
        if ($secondUser) {
            Administration::firstOrCreate(
                [
                    'user_id' => $secondUser->id,
                    'leave_category' => 'izin_pribadi',
                    'start_date' => Carbon::now()->addDays(2)->toDateString(),
                ],
                [
                    'end_date' => Carbon::now()->addDays(3)->toDateString(),
                    'lama_hari_cuti' => '2',
                    'description' => 'Izin keperluan keluarga mendesak mengurus administrasi kependudukan di kampung halaman.',
                    'bring_laptop' => false,
                    'contacted' => true,
                    'status' => 'pending',
                ]
            );
        }
    }
}
