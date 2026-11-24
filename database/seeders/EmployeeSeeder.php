<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // Temukan role berdasarkan nama
        $roles = [
            'Analyst' => Role::where('name', 'Analyst')->first(),
            'Project Director' => Role::where('name', 'Project Director')->first(),
            'Designer' => Role::where('name', 'Designer')->first(),
            'Engineer Web' => Role::where('name', 'Engineer Web')->first(),
            'Engineer Mobile' => Role::where('name', 'Engineer Mobile')->first(),
            'Engineer Tester' => Role::where('name', 'Engineer Tester')->first(),
        ];

        // Data employees
        $employees = [
            // Analysts
            ['name' => 'Adityo', 'role' => 'Analyst'],
            ['name' => 'Atika', 'role' => 'Analyst'],
            ['name' => 'Devine', 'role' => 'Analyst'],
            ['name' => 'Firli', 'role' => 'Analyst'],
            ['name' => 'Reza', 'role' => 'Analyst'],

            // Project Directors (role akan diubah ke admin di tabel users)
            ['name' => 'Alfian', 'role' => 'Project Director'],
            ['name' => 'Rino', 'role' => 'Project Director'],
            ['name' => 'Riki', 'role' => 'Project Director'],

            // Designers
            ['name' => 'Assabil', 'role' => 'Designer'],
            ['name' => 'David', 'role' => 'Designer'],

            // Engineers Web
            ['name' => 'Adam', 'role' => 'Engineer Web'],
            ['name' => 'Aditama', 'role' => 'Engineer Web'],
            ['name' => 'Hanif', 'role' => 'Engineer Web'],
            ['name' => 'Mahfud', 'role' => 'Engineer Web'],
            ['name' => 'Wildan', 'role' => 'Engineer Web'],

            // Engineers Mobile
            ['name' => 'Ady', 'role' => 'Engineer Mobile'],
            ['name' => 'Angga', 'role' => 'Engineer Mobile'],
            ['name' => 'Ridho', 'role' => 'Engineer Mobile'],
            ['name' => 'Yazid', 'role' => 'Engineer Mobile'],

            // Engineers Tester
            ['name' => 'Dinda', 'role' => 'Engineer Tester'],
            ['name' => 'Enggar', 'role' => 'Engineer Tester'],
        ];

        // Kumpulkan data untuk users
        $usersData = [];
        $employeesData = [];

        foreach ($employees as $employeeData) {
            $role = $employeeData['role'] === 'Project Director' ? 'admin' : 'user'; // Ubah role ke admin untuk Project Director

            $usersData[] = [
                'name' => $employeeData['name'], // Username
                'email' => strtolower($employeeData['name']) . '@example.com', // Dummy email
                'password' => Hash::make('password123'), // Dummy password
                'role' => $role, // Role admin untuk Project Director
                'created_at' => now(), // Tambahkan timestamp untuk insert
                'updated_at' => now(),
            ];
        }

        // Insert data ke tabel users
        User::insert($usersData);

        // Ambil semua user yang baru saja dimasukkan
        $insertedUsers = User::whereIn('email', array_column($usersData, 'email'))->get();

        // Buat mapping untuk mencocokkan user dan employee
        foreach ($insertedUsers as $index => $user) {
            $employeeData = $employees[$index];

            $employeesData[] = [
                'user_id' => $user->id,
                'role_id' => $roles[$employeeData['role']]->id,
                'work_email' => strtolower($employeeData['name']) . '@company.com', // Dummy work email
                'photo' => null,
                'nik' => null,
                'status' => null,
                'birth_date' => null,
                'phone_number' => null,
                'telegram_link' => null,
                'address' => null,
                'join_date' => null,
                'education' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert data ke tabel employees
        Employee::insert($employeesData);
    }
}
