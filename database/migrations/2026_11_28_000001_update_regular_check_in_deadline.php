<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_settings', function (Blueprint $table) {
            $table->time('check_in_end')->default('08:00:00')->change();
        });

        DB::table('office_settings')
            ->where('check_in_end', '08:30:00')
            ->update(['check_in_end' => '08:00:00']);
    }

    public function down(): void
    {
        Schema::table('office_settings', function (Blueprint $table) {
            $table->time('check_in_end')->default('08:30:00')->change();
        });
    }
};
