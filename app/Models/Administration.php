<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administration extends Model
{
    use HasFactory;

    protected $table = 'administrations'; // Pastikan ini sesuai dengan nama tabel di database
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    protected $fillable = [
        'user_id',
        'leave_category',
        'lama_hari_cuti',
        'start_date',
        'end_date',
        'description',
        'bring_laptop',
        'contacted',
        'status',
        'approver_id',
        'approver_note',
        'attachment',
    ];

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function leaveCategory()
    {
        return $this->belongsTo(LeaveCategory::class, 'leave_category');
    }

}

