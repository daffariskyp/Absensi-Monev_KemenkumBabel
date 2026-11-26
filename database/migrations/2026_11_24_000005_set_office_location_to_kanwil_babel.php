<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\OfficeSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('office_settings')) {
            $setting = OfficeSetting::first();
            if ($setting) {
                $setting->update([
                    'office_name' => 'Kanwil Kemenkumham Kepulauan Bangka Belitung',
                    'latitude' => -2.1516954,
                    'longitude' => 106.1568149,
                    'radius_meters' => 350,
                    'check_in_start' => '06:00:00',
                    'check_in_end' => '08:30:00',
                    'check_out_start' => '16:00:00',
                ]);
            } else {
                OfficeSetting::create([
                    'office_name' => 'Kanwil Kemenkumham Kepulauan Bangka Belitung',
                    'latitude' => -2.1516954,
                    'longitude' => 106.1568149,
                    'radius_meters' => 350,
                    'check_in_start' => '06:00:00',
                    'check_in_end' => '08:30:00',
                    'check_out_start' => '16:00:00',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
