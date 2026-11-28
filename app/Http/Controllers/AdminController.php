<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }

            abort(403, 'Akses ditolak: Menu manajemen SDM hanya dapat diakses oleh Administrator Utama.');
        });
    }

    public function index(Request $request)
    {
        // // Periksa apakah pengguna adalah admin
        // $user = auth()->user(); // Mendapatkan pengguna yang sedang login
        // if ($user->role !== 'admin') {
        //   abort(403, 'Anda tidak memiliki akses ke halaman ini.'); // Forbidden
        // }

        // Ambil query untuk search dan filter
        $search = $request->input('search');
        $roleFilter = $request->input('role');

        // Query employees melalui relasi
        $query = Employee::with(['user', 'role']);

        // Filter berdasarkan search
        if (!empty($search)) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%') // Filter nama user
                  ->orWhere('email', 'LIKE', '%' . $search . '%'); // Filter email user
            });
        }

        // Filter berdasarkan role (opsional)
        if (!empty($roleFilter)) {
            $query->where('role_id', $roleFilter);
        }

        // Tampilkan seluruh pegawai dalam 1 halaman utama (hingga 100 pegawai) agar tombol Hapus tidak terpotong pagination
        $employees = $query->paginate(15);

        // Ambil semua role
        $roles = Role::whereIn('name', Role::DIVISIONS)->orderBy('name')->get();
        return view('admin.index', [
          "title" => "Admin",
          "active" => "admin",
          "roles" => $roles,
          "employees" => $employees,
          "search" => $search,
          "roleFilter" => $roleFilter,
        ]);
    }

    public function create()
    {
        // // Periksa apakah pengguna adalah admin
        // $user = auth()->user(); // Mendapatkan pengguna yang sedang login
        // if ($user->role !== 'admin') {
        //   abort(403, 'Anda tidak memiliki akses ke halaman ini.'); // Forbidden
        // }

        $roles = Role::whereIn('name', Role::DIVISIONS)->orderBy('name')->get();
        return view('admin.create', [
          "title" => "Create User",
          "active" => "admin",
          "roles" => $roles,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
              'name' => 'required|string|max:255',
              'email' => 'required|email|unique:users,email',
              'password' => 'required|min:8|confirmed',
              'role_id' => ['required', Rule::exists('roles', 'id')->whereIn('name', Role::DIVISIONS)],
              'system_role' => 'nullable|in:admin,supervisor,user',
            ]);

            $systemRole = $validated['system_role'] ?? 'user';

            $user = User::create([
              'name' => $validated['name'],
              'email' => $validated['email'],
              'password' => $validated['password'],
              'role' => $systemRole,
            ]);

            Employee::create([
              'user_id' => $user->id,
              'role_id' => $validated['role_id'],
              'work_email' => $validated['email'],
            ]);

            return redirect()->route('admin.index')->with('success', 'Akun SDM berhasil dibuat!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->validator);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat akun SDM: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $employee = Employee::with(['user', 'role'])->findOrFail($id);
        $roles = Role::whereIn('name', Role::DIVISIONS)->orderBy('name')->get();
        $statuses = Employee::getStatuses();

        return view('admin.edit', [
          'title' => 'Edit Employee',
          'active' => 'admin',
          'employee' => $employee,
          'roles' => $roles,
          'statuses' => $statuses,
        ]);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
          'name' => 'nullable|string|max:255',
          'system_role' => 'nullable|in:admin,supervisor,user',
          'role_id' => ['nullable', Rule::exists('roles', 'id')->whereIn('name', Role::DIVISIONS)],
          'password' => 'nullable|min:8|confirmed',
          'work_email' => 'nullable|email|unique:employees,work_email,' . $employee->id,
          'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
          'nik' => 'nullable|string|max:255|unique:employees,nik,' . $employee->id,
          'status' => 'nullable|in:Kontrak,Freelance,Tetap,Tenaga Ahli',
          'birth_date' => 'nullable|date',
          'phone_number' => 'nullable|string|max:15',
          'telegram_link' => 'nullable|string|max:1000',
          'address' => 'nullable|string|max:255',
          'join_date' => 'nullable|date',
          'education' => 'nullable|string|max:255',
        ], [
          'photo.uploaded' => 'Foto gagal diunggah. Pilih file JPG, PNG, atau WebP hingga 5 MB.',
          'photo.image' => 'Pilih file foto yang valid.',
          'photo.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
          'photo.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $userUpdates = array_filter([
            'name' => $validated['name'] ?? null,
            'role' => $validated['system_role'] ?? null,
            'password' => isset($validated['password']) ? Hash::make($validated['password']) : null,
        ], fn ($value) => $value !== null);

        $newPhoto = null;
        if ($request->hasFile('photo')) {
            $newPhoto = $request->file('photo')->store('users-image', 'public');
            if ($newPhoto === false) {
                throw ValidationException::withMessages(['photo' => 'Foto gagal ditulis ke penyimpanan.']);
            }
            $validated['photo'] = $newPhoto;
        }

        try {
            $previousPhoto = DB::transaction(function () use ($employee, $validated, $userUpdates) {
                $current = Employee::lockForUpdate()->findOrFail($employee->id);
                $previousPhoto = $current->photo;
                $current->update($validated);
                if ($userUpdates) {
                    $current->user?->update($userUpdates);
                }

                return $previousPhoto;
            });
        } catch (\Throwable $e) {
            if ($newPhoto) {
                Storage::disk('public')->delete($newPhoto);
            }
            throw $e;
        }

        if ($newPhoto && $previousPhoto && $previousPhoto !== $newPhoto) {
            Storage::disk('public')->delete($previousPhoto);
        }

        return redirect()->route('admin.index')->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $user = $employee->user;

            $employee->delete();
            $user->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
