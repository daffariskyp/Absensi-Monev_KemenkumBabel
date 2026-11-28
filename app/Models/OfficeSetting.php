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
        'satpam_pagi_in_start',
        'satpam_pagi_in_end',
        'satpam_pagi_out_start',
        'satpam_malam_in_start',
        'satpam_malam_in_end',
        'satpam_malam_out_start',
    ];

    /**
     * Get or create the default office setting
     */
    public static function getActive()
    {
        $setting = static::first();
        if (!$setting) {
            $setting = static::create([
                'office_name' => 'Kantor Instansi Pegawai Non-ASN',
                'latitude' => -6.1753924,
                'longitude' => 106.8271528,
                'radius_meters' => 250,
                'check_in_start' => '06:00:00',
                'check_in_end' => '08:00:00',
                'check_out_start' => '16:00:00',
                'satpam_pagi_in_start' => '06:30:00',
                'satpam_pagi_in_end' => '08:00:00',
                'satpam_pagi_out_start' => '19:00:00',
                'satpam_malam_in_start' => '18:30:00',
                'satpam_malam_in_end' => '20:00:00',
                'satpam_malam_out_start' => '07:00:00',
            ]);
        }
        return $setting;
    }
}
