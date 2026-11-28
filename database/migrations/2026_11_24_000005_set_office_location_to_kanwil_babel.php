<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('office_settings')
            ->whereIn('office_name', ['Kantor Instansi Pegawai Non-ASN', 'Kantor Utama Non-ASN'])
            ->where('latitude', -6.1753924)
            ->where('longitude', 106.8271528)
            ->update([
                'office_name' => 'Kanwil Kemenkumham Kepulauan Bangka Belitung',
                'latitude' => -2.1516954,
                'longitude' => 106.1568149,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
