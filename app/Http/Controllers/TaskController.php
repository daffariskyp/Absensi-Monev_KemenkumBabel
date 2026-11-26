<?php

namespace App\Http\Controllers;

use App\Events\SendNotification;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectEmployee;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\TryCatch;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {

    }

    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->role == 'admin';
        $hasEmployeeRecord = $user->employee ? true : false;

        $projectsQuery = Project::query()->whereIn('project_status_id', [2, 3]);
        if (!$isAdmin) {
            $projectsQuery->whereHas('employees', function ($query) use ($user) {
                $query->where('employee_id', $user->employee->id)
                      ->where('isformeremployee', false);
            });
        } elseif ($isAdmin && $hasEmployeeRecord) {
            $projectsQuery->whereHas('employees', function ($query) use ($user) {
                $query->when('roles.leader_id', function ($sq) use ($user) {
                    $sq->where('employees.role_id', $user->employee->role_id);
                });
                $query->where('employee_id', $user->employee->id)
                      ->where('isformeremployee', false);
            });
        } else {
            $projectsQuery->whereHas('employees', function ($query) {
                $query->where('isformeremployee', false);
            });
        }

        $projects = $projectsQuery->with(['employees' => function ($query) use ($user, $isAdmin) {
            $query->where('isformeremployee', false);

            if (!$isAdmin && $user->employee) {
                $query->where('employee_id', $user->employee->id);
            }

            $query->with('user');
        }])->get();

        return view('task.index', [
            'title' => 'Tasks',
            'active' => 'tasks',
            'projects' => $projects,
        ]);
    }

    public function getTasks(Request $request)
    {
        /*
          1. ambil semua project termasuk relasi tasks dan employees dll yang dibutuhkan
          2. jika user adalah admin, maka ambil semua project
          3. jika user bukan admin, maka ambil project yang memiliki employee_id yang sama dengan employee_id user
          4. jika request memiliki query date, maka return semua data yang sudah difilter berdasarkan admin dan created_at berdasarkan query date
          5. jika request tidak memiliki query date maka filter project dimana created_at = variable date dan task_status_id = 1
        */
        $user = auth()->user();
        $date = $request->query('date', now()->toDateString());
        $isToday = trim($date) === now()->toDateString();

        $isAppAdmin = !$user->employee;
        $isProjectAdmin = $user->employee?->role?->name === 'Project Director';
        $isTeamLeader = $user->employee && $user->employee->id === $user->employee->role?->leader_id;

        $query = Project::query();

        $tasksQuery = function ($query) use ($date, $user, $isProjectAdmin, $isAppAdmin, $isToday, $isTeamLeader) {
            $query->where(function ($q) use ($date, $isToday) {
                $q->whereDate('created_at', $date);
                if ($isToday) {
                    $q->orWhere('task_status_id', 1);
                }
            });

            if (!$isAppAdmin) {
                if ($isTeamLeader) {
                    $query->whereHas('assignedProjectEmployee.employee', function ($q) use ($user) {
                        $q->where('role_id', $user->employee->role_id);
                    });
                } elseif (!$isProjectAdmin) {
                    $query->whereHas('assignedProjectEmployee', function ($q) use ($user) {
                        $q->where('employee_id', $user->employee->id);
                    });
                } else {
                    $query->whereHas('project.employees', function ($q) use ($user) {
                        $q->where('employee_id', $user->employee->id);
                    });
                }
            }
        };

        $employeesQuery = function ($query) use ($user, $isProjectAdmin, $isAppAdmin, $isTeamLeader) {
            if (!$isAppAdmin) {
                if ($isTeamLeader) {
                    $query->where('role_id', $user->employee->role_id);
                } elseif (!$isProjectAdmin) {
                    $query->where('employee_id', $user->employee->id);
                }
            }

            $query->select([
                'employees.*',
                'project_employees.isformeremployee',
                'project_employees.id as projectEmployeeid'
            ]);
        };

        $query->whereIn('project_status_id', [2, 3]);

        if (!$isAppAdmin) {

            if ($isTeamLeader) {
                $query->whereHas('employees', function ($q) use ($user) {
                    $q->where(function ($subquery) use ($user) {
                        $subquery->where('role_id', $user->employee->role_id)
                                 ->where('isformeremployee', false);
                    })
                    ->orWhere(function ($subquery) use ($user) {
                        $subquery->where('employee_id', $user->employee->id)
                                 ->where('isformeremployee', false);
                    });
                });
            } else {
                $query->whereHas('employees', function ($q) use ($user) {
                    $q->where('employee_id', $user->employee->id)
                      ->where('isformeremployee', false);
                });
            }


        }

        $query->with([
            'employees' => $employeesQuery,
            'employees.user',
            'employees.role',
            'tasks' => $tasksQuery,
            'tasks.taskStatus',
            'tasks.taskLevel',
            'tasks.assignedProjectEmployee',
            'tasks.timeLog',
        ]);

        $projects = $query->get();
        return response()->json($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'name' => 'required|string|max:255',
                'task_status_id' => 'required|exists:task_statuses,id',
                'task_level_id' => 'required|exists:task_levels,id',
                'assigned_project_employee_id' => 'required|exists:project_employees,id',
                'accepted' => 'nullable|nullable|in:true,false,1,0'
            ]);
            $task = Task::create($validated)->load(['taskLevel', 'taskStatus', 'assignedProjectEmployee', 'timeLog']);

            // Ambil employee_id dari project_employees
            $projectEmployee = ProjectEmployee::findOrFail($validated['assigned_project_employee_id']);

            if ($validated['accepted']) {
                Employee::find($projectEmployee->employee_id)->update(['status_employee' => 'Ready']);
            }

            $notification = json_decode(json_encode([
                'employee_id' => $projectEmployee->employee_id,
                'task' => $task
            ]), true);

            SendNotification::dispatch($notification);

            return redirect()->route('tasks.index')
                ->with('success', 'Task ' . $validated['name'] . ' created successfully.');
        } catch (\Throwable $th) {
            Log::error('error creating task ' . $th);
            return back()->withErrors(['error' => 'Something went wrong. Please try again or contact support']);
        }
    }

    // show all task
    // Jika bukan admin, hanya ambil project yang memiliki employee tertentu
    public function allTask(Request $request)
    {
        $user = auth()->user();
        $isAdmin = !$user->employee;
        $isTeamLeader = $user->employee && $user->employee->id === $user->employee->role?->leader_id;

        // Base query
        $tasksQuery = Task::with(['project', 'taskStatus', 'taskLevel', 'assignedProjectEmployee'])->whereHas('project', function ($q) {
            $q->whereIn('project_status_id', [2,3]);
        });


        if (!$isAdmin) {
            if ($isTeamLeader) {
                $tasksQuery->whereHas('assignedProjectEmployee', function ($query) use ($user) {
                    $query->whereHas('employee', function ($q) use ($user) {
                        $q->where('role_id', $user->employee->role_id);
                    });
                });
            } else {
                $tasksQuery->whereHas('assignedProjectEmployee', function ($query) use ($user) {
                    $query->where('employee_id', $user->employee->id);
                });
            }
        }

        // Filter task level
        if ($request->has('task_level') && $request->task_level) {
            $tasksQuery->whereHas('taskLevel', function ($query) use ($request) {
                $query->where('name', $request->task_level);
            });
        }

        // Filter task status
        if ($request->has('task_status') && $request->task_status) {
            $tasksQuery->whereHas('taskStatus', function ($query) use ($request) {
                $query->where('name', $request->task_status);
            });
        }

        if ($request->has('project') && $request->project) {
            $tasksQuery->where('project_id', $request->project)->whereHas('employees', function ($q) use ($user) {
                $q->where('employees.role_id', $user->employee->role_id);
            });
        }

        if ($request->has('employee') && $request->employee) {
            $projectEmployeeIds = DB::table('project_employees')
                ->where('employee_id', $request->employee)
                ->pluck('id')
                ->toArray();

            $tasksQuery->whereIn('assigned_project_employee_id', $projectEmployeeIds);
        }

        // Paginate the results
        $tasks = $tasksQuery->orderBy('created_at', 'desc')->paginate(10);
        $employees = User::has('employee')
            ->with('employee')
            ->orderBy('name')
            ->get();

        if ($isAdmin) {
            $projects = Project::whereIn('project_status_id', [2,3])->get();
        } elseif ($isTeamLeader) {
            $projects = Project::whereHas('employees', function ($sq) use ($user) {
                $sq->where('employees.role_id', $user->employee->role_id);
            })->whereIn('project_status_id', [2,3])->get();
        } else {

            $projects = Project::whereHas('employees', function ($q) use ($user) {
                $q->where('employee_id', $user->employee->id);
            })->whereIn('project_status_id', [2,3])->get();
        }

        return view('task.all_task', [
            'title' => 'Task',
            'active' => 'tasks',
            'tasks' => $tasks,
            'employees' => $employees,
            'projects' => $projects
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, task $task)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'task_level_id' => 'required|exists:task_levels,id',
            ]);

            $task->update($validated);

            return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
        } catch (\Throwable $th) {
            Log::error('Error updating task ' . $th);
            return back()->withErrors(['error' => 'Something went wrong. Please try again or contact support']);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        // Validasi hanya task_status_id untuk pembaruan status
        $validated = $request->validate([
            'task_status_id' => 'required|exists:task_statuses,id',
        ]);
        // Temukan task berdasarkan id yang diberikan
        $task = Task::findOrFail($id);

        // Update task_status_id
        $task->task_status_id = $validated['task_status_id'];
        $task->save();

        $task->load('timeLog', 'taskStatus');

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'task' => $task,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function updateAccepted(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->update(['accepted' => true]);
            $task->assignedProjectEmployee->employee->update(['status_employee' => 'Ready']);

            return response()->json([
                'success' => true,
                'message' => 'task updated successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task'
            ]);
        }
    }
}
