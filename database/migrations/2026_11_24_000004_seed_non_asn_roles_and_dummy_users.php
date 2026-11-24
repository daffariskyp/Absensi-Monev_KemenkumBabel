<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\NonAsnDummySeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seeder = new NonAsnDummySeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to delete dummy data on rollback
    }
};
