<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public const DIVISIONS = ['Petugas Kebersihan', 'Satpam', 'Driver', 'Kabag (Kepala Bagian)'];

    protected $fillable = ['name'];

    // Relasi one-to-many dengan tabel employees
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
