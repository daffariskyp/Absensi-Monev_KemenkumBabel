<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'date',
        'check_in_time',
        'check_in_photo',
        'check_in_lat',
        'check_in_lng',
        'check_in_distance',
        'check_in_status',
        'check_out_time',
        'check_out_photo',
        'check_out_lat',
        'check_out_lng',
        'check_out_distance',
        'monev_activity',
        'monev_obstacle',
        'activity_photos',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'activity_photos' => 'array',
        'check_in_distance' => 'integer',
        'check_out_distance' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
