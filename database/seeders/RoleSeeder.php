<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        foreach (Role::DIVISIONS as $name) {
            Role::firstOrCreate(['name' => $name]);
        }
    }
}

