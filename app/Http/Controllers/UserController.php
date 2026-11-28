<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function show()
    {
        $user = auth()->user()->load('employee.role');
        return view('users.show', [
            'title' => 'Profil Saya',
            'active' => 'users',
            'user' => $user,
        ]);
    }

    public function edit($id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ?string $user = null)
    {
        $account = $request->user();
        $employee = $account->employee;
        if ($user !== null) {
            abort_unless($employee && (string) $employee->getKey() === $user, 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'work_email' => ['nullable', 'email', 'max:255', Rule::unique('employees')->ignore($employee?->id)],
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'bio' => 'nullable|string|max:1000',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'phone_number' => 'nullable|string|max:15',
            'telegram_link' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'education' => 'nullable|string|max:255',
        ], [
            'photo.uploaded' => 'Foto gagal diunggah. Pilih file JPG, PNG, atau WebP hingga 5 MB.',
            'photo.image' => 'Pilih file foto yang valid.',
            'photo.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'photo.max' => 'Ukuran foto maksimal 5 MB.',
        ]);

        $profile = Arr::except($validated, ['name', 'photo']);
        $newPhoto = null;
        if ($request->hasFile('photo')) {
            $newPhoto = $request->file('photo')->store('users-image', 'public');
            if ($newPhoto === false) {
                throw ValidationException::withMessages(['photo' => 'Foto gagal ditulis ke penyimpanan.']);
            }
            $profile['photo'] = $newPhoto;
        }

        try {
            $previousPhoto = DB::transaction(function () use ($account, $profile, $validated) {
                $current = User::lockForUpdate()->findOrFail($account->id);
                $employee = $current->employee()->lockForUpdate()->first();
                $previousPhoto = $employee?->photo;

                if ($profile) {
                    $employee ??= $current->employee()->make();
                    $employee->fill($profile)->save();
                }

                if (isset($validated['name'])) {
                    $current->update(['name' => $validated['name']]);
                }

                return $previousPhoto;
            });
        } catch (\Throwable $exception) {
            if ($newPhoto) {
                Storage::disk('public')->delete($newPhoto);
            }
            throw $exception;
        }

        if ($newPhoto && $previousPhoto) {
            Storage::disk('public')->delete($previousPhoto);
        }

        return redirect()->route('users.profile')->with('profile_saved', 'Profil berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
