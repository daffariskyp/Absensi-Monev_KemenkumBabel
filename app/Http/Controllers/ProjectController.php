<?php

namespace App\Http\Controllers;

use App\Jobs\BroadcastEmailJob;
use App\Models\Employee;
use App\Models\Project;
use App\Models\ProjectLevel;
use App\Models\ProjectStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    private $commonData = null;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if ($this->isAuthorized()) {
                return $next($request);
            }

            return redirect()->route('projects.index');
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Project::with([
            'employees.user',
            'employees.role',
            'status',
            'level'
        ]);

        $query = $this->applyFilters($query, $request, $user);
        $projects = $query->paginate(10);

        

        // Get directors for filter dropdown
        $directors = Employee::with('user')
            ->whereHas('role', fn ($q) => $q->where('name', 'Project Director'))
            ->get();

        return view('project.index', [
            'title' => 'Projects',
            'active' => 'projects',
            'projects' => $projects,
            'directors' => $directors,
            'statuses' => ProjectStatus::all(),
        ]);
    }

    private function applyFilters(Builder $query, Request $request, $user)
    {
        if ($user->employee) {
            $query->whereHas('employees', function ($q) use ($user) {
                $q->where('employees.id', $user->employee->id)
                  ->where('project_employees.isformeremployee', 0);
            });
        }

        // Apply other filters
        if ($level = $request->input('project_level')) {
            $query->whereHas('level', fn ($q) => $q->where('name', $level));
        }

        if ($director = $request->input('project_director')) {
            $query->whereHas('employees', function ($q) use ($director) {
                $q->where('employees.id', $director)
                  ->where('project_employees.isformeremployee', 0)
                  ->whereHas('role', fn ($q) => $q->where('name', 'Project Director'));
            });
        }

        if ($status = $request->input('project_status')) {
            $query->where('project_status_id', $status);
        }

        return $query;
    }

    public function create()
    {
        $employees = Employee::with(['user', 'role'])->get();
        
        // Tambahkan log untuk debugging dengan null safety
        foreach ($employees as $employee) {
            \Log::info("Karyawan: " . ($employee->user?->name ?? 'N/A') . ", Role: " . ($employee->role?->name ?? 'No Role'));
        }
    
        return view('project.create', array_merge([
            'title' => 'Create Project',
            'active' => 'projects',
        ], $this->getFormData()));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'project_level_id' => 'required|exists:project_levels,id',
            'project_status_id' => 'required|exists:project_statuses,id',
            'director_id' => 'required|exists:employees,id',
            'satpam_id' => 'nullable|exists:employees,id',
            'driver_id' => 'nullable|exists:employees,id',
            'petugas_kebersihan_id' => 'nullable|exists:employees,id',
            'engineer_web_id' => 'nullable|exists:employees,id',
            'analyst_id' => 'nullable|exists:employees,id',
            'engineer_android_id' => 'nullable|exists:employees,id',
            'engineer_IOS_id' => 'nullable|exists:employees,id',
            'content_creator_id' => 'nullable|exists:employees,id',
            'designer_id' => 'nullable|exists:employees,id',
            'engineer_tester_id' => 'nullable|exists:employees,id',
            'copy_writer_id' => 'nullable|exists:employees,id',
        ]);
    
        DB::beginTransaction();
        
        try {
            $project = Project::create($validated);
    
            $employeeIds = collect([
                $request->director_id,
                $request->satpam_id,
                $request->driver_id,
                $request->petugas_kebersihan_id,
                $request->engineer_web_id,
                $request->analyst_id,
                $request->engineer_android_id,
                $request->engineer_IOS_id,
                $request->content_creator_id,
                $request->designer_id,
                $request->engineer_tester_id,
                $request->copy_writer_id,
            ])->filter()->all();
    
            // Update status employee menjadi "Stand By"
            if (!empty($employeeIds)) {
                Employee::whereIn('id', $employeeIds)->update(['status_employee' => 'Stand By']);
    
                // Associate employees with the project
                $project->employees()->sync($employeeIds);
            }
    
            DB::commit();
    
            // Notifikasi sukses menggunakan notify()
            notify()->success('Project Berhasil Dibuat', 'Success');
            return redirect()->route('projects.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating project: ' . $e->getMessage());
    
            // Notifikasi error menggunakan notify()
            notify()->error('Gagal membuat project. ' . $e->getMessage(), 'Error');
            return back()->withInput();
        }
    }
    
    

    public function show(string $id)
    {
        $project = Project::with(['level', 'status', 'employees.user','employees.role'])->findOrFail($id);

        return view('project.show', [
            'title' => 'Project Details',
            'active' => 'projects',
            'project' => $project,
        ]);
    }

    public function edit(Project $project)
    {
        $project->load(['employees' => function ($q) {
            $q->where('isformeremployee', false);
        }, 'level', 'status']);

        $employees = $project->employees;

        $roles = [
            'director' => 'Project Director',
            'satpam' => 'Satpam',
            'driver' => 'Driver',
            'petugasKebersihan' => 'Petugas Kebersihan',
            'engineerWeb' => 'Engineer Web',
            'analyst' => 'Analyst',
            'engineerAndroid' => 'Engineer Android',
            'engineerIos' => 'Engineer IOS',
            'contentCreator' => 'Content Creator',
            'designer' => 'Designer',
            'engineerTester' => 'Engineer Tester',
            'copyWriter' => 'Copy Writer',
        ];

        $employeeByRole = [];
        foreach ($roles as $key => $roleName) {
            $employeeByRole[$key] = $employees->first(function ($emp) use ($roleName) {
                if ($emp->role?->name === $roleName) {
                    return true;
                }
                if (in_array($roleName, ['Engineer Android', 'Engineer IOS']) && in_array($emp->role?->name, ['Engineer Mobile', 'Engineer Android', 'Engineer IOS'])) {
                    return true;
                }
                return false;
            });
        }

        return view('project.edit', array_merge([
            'title' => 'Edit Project',
            'active' => 'projects',
            'project' => $project,
            'employeeIds' => $employees->pluck('id')->toArray(),
            'director' => $employeeByRole['director'],
            'satpam' => $employeeByRole['satpam'],
            'driver' => $employeeByRole['driver'],
            'petugasKebersihan' => $employeeByRole['petugasKebersihan'],
            'engineerWeb' => $employeeByRole['engineerWeb'],
            'analyst' => $employeeByRole['analyst'],
            'engineerAndroid' => $employeeByRole['engineerAndroid'],
            'engineerIos' => $employeeByRole['engineerIos'],
            'contentCreator' => $employeeByRole['contentCreator'],
            'designer' => $employeeByRole['designer'],
            'engineerTester' => $employeeByRole['engineerTester'],
            'copyWriter' => $employeeByRole['copyWriter'],

        ], $this->getFormData()));
    }

    public function update(Request $request, Project $project)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'project_level_id' => 'required|exists:project_levels,id',
                'project_status_id' => 'required|exists:project_statuses,id',
                'director_id' => 'nullable|exists:employees,id',
                'satpam_id' => 'nullable|exists:employees,id',
                'driver_id' => 'nullable|exists:employees,id',
                'petugas_kebersihan_id' => 'nullable|exists:employees,id',
                'engineer_web_id' => 'nullable|exists:employees,id',
                'analyst_id' => 'nullable|exists:employees,id',
                'engineer_android_id' => 'nullable|exists:employees,id',
                'engineer_IOS_id' => 'nullable|exists:employees,id',
                'content_creator_id' => 'nullable|exists:employees,id',
                'designer_id' => 'nullable|exists:employees,id',
                'engineer_tester_id' => 'nullable|exists:employees,id',
                'copy_writer_id' => 'nullable|exists:employees,id',
            ]);
    
            $project->update($validated);
    
            $newEmployeeIds = collect([
                $request->director_id,
                $request->satpam_id,
                $request->driver_id,
                $request->petugas_kebersihan_id,
                $request->engineer_web_id,
                $request->analyst_id,
                $request->engineer_android_id,
                $request->engineer_IOS_id,
                $request->content_creator_id,
                $request->designer_id,
                $request->engineer_tester_id,
                $request->copy_writer_id,
            ])->filter()->all();
            
    
            $currentEmployeeIds = $project->employees()
                ->where('isformeremployee', 0)
                ->pluck('employee_id')
                ->toArray();
    
            $employeesToAdd = array_diff($newEmployeeIds, $currentEmployeeIds);
            $employeesToRemove = array_diff($currentEmployeeIds, $newEmployeeIds);
    
            // Handle employees to add
            foreach ($employeesToAdd as $employeeId) {
                $existingRecord = DB::table('project_employees')
                    ->where('project_id', $project->id)
                    ->where('employee_id', $employeeId)
                    ->first();
    
                if ($existingRecord) {
                    DB::table('project_employees')
                        ->where('id', $existingRecord->id)
                        ->update(['isformeremployee' => 0]);
                } else {
                    $project->employees()->attach($employeeId);
                }
            }
    
            // Handle employees to remove (set isformeremployee = true)
            if (!empty($employeesToRemove)) {
                DB::table('project_employees')
                    ->where('project_id', $project->id)
                    ->whereIn('employee_id', $employeesToRemove)
                    ->update(['isformeremployee' => 1]);
            }
    
            DB::commit();
    
            // ✅ Notifikasi sukses menggunakan notify()
            notify()->success('Project Berhasil Diperbarui', 'Success');
            return redirect()->route('projects.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating project: ' . $e->getMessage());
    
            // ❌ Notifikasi error menggunakan notify()
            notify()->error('Gagal memperbarui project. ' . $e->getMessage(), 'Error');
            return back()->withInput();
        }
    }
    

    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting project: ' . $e->getMessage());
            return redirect()->route('projects.index')->with('error', 'Failed to delete project.');
        }
    }

    private function getFormData(): array
    {
        if ($this->commonData !== null) {
            return $this->commonData;
        }
        $roles = [
            'Project Director', 'Engineer Web', 'Analyst', 'Engineer Android',
            'Engineer IOS', 'Content Creator', 'Designer', 'Engineer Tester', 'Copy Writer'
        ];
        

        $this->commonData = [
            'levels' => ProjectLevel::all(),
            'statuses' => ProjectStatus::all(),
            'employees' => Employee::with(['user', 'role'])->get(),
            'roles' => \App\Models\Role::all(),
        ];

        return $this->commonData;
    }

    private function isAuthorized(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return $user->role === 'admin' ||
               ($user->employee && $user->employee->role?->name === 'Project Director');
    }
}