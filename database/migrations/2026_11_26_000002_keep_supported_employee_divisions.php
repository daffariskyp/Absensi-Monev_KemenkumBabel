<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $divisions = [
                'Petugas Kebersihan' => ['petugas kebersihan', 'kebersihan'],
                'Satpam' => ['satpam'],
                'Driver' => ['driver'],
                'Kabag (Kepala Bagian)' => ['kabag (kepala bagian)', 'kabag', 'kepala bagian'],
            ];
            $roles = DB::table('roles')->orderBy('id')->get();
            $keepIds = [];

            foreach ($divisions as $name => $aliases) {
                $matching = $roles->filter(fn ($role) => in_array(strtolower(trim($role->name)), $aliases, true));
                $primary = $matching->firstWhere('name', $name) ?? $matching->first();

                if (!$primary) {
                    $keepIds[] = DB::table('roles')->insertGetId([
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    continue;
                }

                $keepIds[] = $primary->id;
                $duplicateIds = $matching->pluck('id')->reject(fn ($id) => $id === $primary->id);
                if ($duplicateIds->isNotEmpty()) {
                    DB::table('employees')->whereIn('role_id', $duplicateIds)->update(['role_id' => $primary->id]);
                }
                if ($primary->name !== $name) {
                    DB::table('roles')->where('id', $primary->id)->update(['name' => $name, 'updated_at' => now()]);
                }
            }

            // Preserve employees and account permissions while removing old division assignments.
            DB::table('employees')->whereNotIn('role_id', $keepIds)->update(['role_id' => null]);
            DB::table('roles')->whereNotIn('id', $keepIds)->delete();
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Removed divisions and their former assignments require a database backup to restore.');
    }
};
