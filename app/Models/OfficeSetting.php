<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_name',
        'latitude',
        'longitude',
        'radius_meters',
        'check_in_start',
        'check_in_end',
        'check_out_start',
    ];

    /**
     * Get or create the default office setting
     */
    public static function getActive()
    {
        $setting = static::first();
        if (!$setting) {
            $setting = static::create([
                'office_name' => 'Kanwil Kemenkumham Kepulauan Bangka Belitung',
                'latitude' => -2.155365,
                'longitude' => 106.163656,
                'radius_meters' => 10000,
                'check_in_start' => '06:00:00',
                'check_in_end' => '08:30:00',
                'check_out_start' => '16:00:00',
            ]);
        } elseif (
            $setting->office_name === 'Kantor Instansi Pegawai Non-ASN' 
            || $setting->latitude < -5.0 
            || $setting->radius_meters <= 250
            || abs($setting->latitude - (-2.1516954)) > 1.0
        ) {
            // Otomatis perbarui dari koordinat lama Jakarta (-6.175) ke Kanwil Babel Kemenkum (-2.151)
            $setting->update([
                'office_name' => 'Kanwil Kemenkumham Kepulauan Bangka Belitung',
                'latitude' => -2.1516954,
                'longitude' => 106.1568149,
                'radius_meters' => 500,
            ]);
        }
        return $setting;
    }
}
