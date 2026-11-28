<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_id',
        'work_email',
        'photo',
        'bio',
        'nik',
        'status_employee',
        'birth_date',
        'phone_number',
        'telegram_link',
        'address',
        'join_date',
        'education',
    ];
    protected $casts = [
        'status_employee' => 'string', // untuk memastikan kolom ini diperlakukan sebagai string
    ];
    public static function getStatuses()
    {
        return ['Kontrak', 'Freelance', 'Tetap', 'Tenaga Ahli'];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_employees', 'project_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_employees');
    }
    public function administration()
    {
        return $this->hasMany(Administration::class, 'user_id');
    }

    /**
     * Cek apakah tanggal tertentu adalah hari kerja untuk karyawan ini.
     * Satpam: Senin - Jumat (5 hari kerja).
     * Non-ASN Lain: Senin - Kamis (4 hari kerja).
     */
    public function isWorkDay(\Carbon\Carbon $date): bool
    {
        $dayOfWeek = $date->dayOfWeek; // 0 = Minggu, 1 = Senin, ..., 5 = Jumat, 6 = Sabtu

        if ($this->isSatpam()) {
            return in_array($dayOfWeek, [
                \Carbon\Carbon::MONDAY,
                \Carbon\Carbon::TUESDAY,
                \Carbon\Carbon::WEDNESDAY,
                \Carbon\Carbon::THURSDAY,
                \Carbon\Carbon::FRIDAY,
            ], true);
        }

        return in_array($dayOfWeek, [
            \Carbon\Carbon::MONDAY,
            \Carbon\Carbon::TUESDAY,
            \Carbon\Carbon::WEDNESDAY,
            \Carbon\Carbon::THURSDAY,
        ], true);
    }

    /**
     * Label hari kerja wajib untuk karyawan.
     */
    public function workDaysLabel(): string
    {
        return $this->isSatpam() ? 'Senin - Jumat' : 'Senin - Kamis';
    }

    /**
     * Apakah karyawan ini memiliki role Satpam.
     */
    public function isSatpam(): bool
    {
        if ($this->relationLoaded('role')) {
            return $this->getRelation('role')?->name === 'Satpam';
        }

        if (empty($this->role_id)) {
            return false;
        }

        return $this->role?->name === 'Satpam';
    }
}
