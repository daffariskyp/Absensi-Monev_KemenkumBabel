<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks'; // Explicitly define the table name

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'task_status_id',
        'task_level_id',
        'assigned_project_employee_id',
        'accepted'
    ];

    /**
     * Relasi ke Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relasi ke TaskStatus
     */
    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class);
    }

    /**
     * Relasi ke TaskLevel (misalnya prioritas atau kesulitan tugas)
     */
    public function taskLevel()
    {
        return $this->belongsTo(TaskLevel::class);
    }

    /**
     * Relasi ke ProjectEmployee (karyawan yang ditugaskan)
     */
    public function assignedProjectEmployee()
    {
        return $this->belongsTo(ProjectEmployee::class, 'assigned_project_employee_id');
    }

    /**
     * Relasi ke TaskTimeLog (jika tugas memiliki pencatatan waktu)
     */
    public function timeLog()
    {
        return $this->hasOne(TaskTimeLog::class);
    }
}