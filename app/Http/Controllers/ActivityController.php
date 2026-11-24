<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ProjectEmployee;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $monthInput = $request->input('month');
        if (! $monthInput) {
            $date = now();
        } else {
            $date = Carbon::createFromFormat('Y-m', $monthInput);
        }

        $employeesQuery = Employee::with('user', 'role');
        $employees = $employeesQuery->paginate(6);
        foreach ($employees as $employee) {
            // Calculate total projects
            $employee->sumProjects = ProjectEmployee::where('employee_id', $employee->id)
                ->distinct('project_id')
                ->count();
            // Get all tasks for current month and year
            $tasks = Task::with(['taskLevel', 'timeLog'])
                ->where('task_status_id', 4)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->whereHas('assignedProjectEmployee', function ($query) use ($employee) {
                    $query->where('employee_id', $employee->id);
                })
                ->get();
            $totalWorkDuration = 0;
            foreach ($tasks as $task) {
                $isLowMedium = $task->task_level_id != 3;
                if ($task->timeLog) {
                    if ($isLowMedium && $task->timeLog->duration > $task->taskLevel->duration) {
                        $duration = $task->taskLevel->duration;
                    } else {
                        $duration = $task->timeLog->duration_seconds;
                    }
                    $totalWorkDuration += $duration;
                }
            }
            $employee->totalWorkDuration = number_format(($totalWorkDuration / (187 * 3600)) * 100, 2);
            $employee->sumTasks = $tasks->count();
        }

        return view('activity.index', [
            'title' => 'Activity',
            'active' => 'activity',
            'employees' => $employees,
        ]);
    }
}
