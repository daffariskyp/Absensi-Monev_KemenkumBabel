<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Satpam'],
            ['name' => 'Driver'],
            ['name' => 'Petugas Kebersihan'],
            ['name' => 'Koordinator Lapangan'],
            ['name' => 'Project Director'],
            ['name' => 'Analyst'],
            ['name' => 'Designer'],
            ['name' => 'Engineer Web'],
            ['name' => 'Engineer Mobile'],
            ['name' => 'Engineer Android'],
            ['name' => 'Engineer IOS'],
            ['name' => 'Content Creator'],
            ['name' => 'Engineer Tester'],
            ['name' => 'Copy Writer'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']]);
        }
    }
}

