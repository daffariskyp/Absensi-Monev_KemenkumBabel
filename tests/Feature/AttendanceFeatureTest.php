<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OfficeSetting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Carbon::parse('2026-11-26 09:00', Attendance::TIMEZONE));
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/attendance');
        $response->assertRedirect('login');
    }

    public function test_satpam_sees_shift_selection_on_attendance_page(): void
    {
        $role = Role::firstOrCreate(['name' => 'Satpam']);
        $user = User::factory()->create([
            'role' => 'karyawan',
        ]);
        Employee::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'status' => 'aktif',
            'nip' => 'SATPAM' . rand(1000, 9999),
        ]);

        OfficeSetting::getActive();

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('Pilih Shift Tugas Jaga Hari Ini');
        $response->assertSee('Shift Pagi');
        $response->assertSee('Shift Malam');
        $response->assertSee('id="shiftInput" value=""', false);
        $response->assertSee('id="checkInActions" class="space-y-4 hidden"', false);
        $response->assertDontSee('Absen Pulang & Monev');
        $response->assertDontSee('id="formCheckOut"', false);
    }

    public function test_non_satpam_does_not_see_shift_selection(): void
    {
        $role = Role::firstOrCreate(['name' => 'Driver']);
        $user = User::factory()->create([
            'role' => 'karyawan',
        ]);
        Employee::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'status' => 'aktif',
            'nip' => 'DRIVER' . rand(1000, 9999),
        ]);

        OfficeSetting::getActive();

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertDontSee('Pilih Shift Tugas Jaga Hari Ini');
        $response->assertSee('id="checkInActions" class="space-y-4 "', false);
        $response->assertDontSee('id="formCheckOut"', false);
    }

    public function test_satpam_checkin_requires_shift(): void
    {
        $role = Role::firstOrCreate(['name' => 'Satpam']);
        $user = User::factory()->create([
            'role' => 'karyawan',
        ]);
        Employee::create([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'status' => 'aktif',
            'nip' => 'SATPAM' . rand(1000, 9999),
        ]);

        $response = $this->actingAs($user)->postJson('/attendance/checkin', [
            'latitude' => -2.1234,
            'longitude' => 106.1234,
            'photo' => 'data:image/jpeg;base64,sample',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['shift']);

        $this->postJson('/attendance/checkin', [
            'latitude' => -2.1234,
            'longitude' => 106.1234,
            'photo' => 'data:image/jpeg;base64,c2FtcGxl',
            'shift' => 'invalid',
        ])->assertUnprocessable()->assertJsonValidationErrors(['shift']);
    }

    public function test_admin_dashboard_and_print_show_friday_schedules(): void
    {
        $this->travelTo(Carbon::parse('2026-11-27 06:00', Attendance::TIMEZONE));
        $admin = User::factory()->create(['role' => 'admin']);
        $driver = User::factory()->create(['role' => 'karyawan']);
        $satpam = User::factory()->create(['role' => 'karyawan']);

        foreach ([[$driver, 'Driver'], [$satpam, 'Satpam']] as [$user, $role]) {
            Employee::create([
                'user_id' => $user->id,
                'role_id' => Role::firstOrCreate(['name' => $role])->id,
                'status' => 'aktif',
            ]);
        }

        $dashboard = $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->assertContains($driver->id, collect($dashboard->viewData('listLiburTerjadwal'))->pluck('employee.user_id'));
        $this->assertContains($satpam->id, collect($dashboard->viewData('listTanpaKeterangan'))->pluck('employee.user_id'));
        $this->get('/dashboard/print-today')->assertOk()->assertSee('Libur Terjadwal');

        $this->actingAs($driver)->get('/attendance')->assertOk()
            ->assertSee('Hari Ini Bukan Hari Kerja Terjadwal')
            ->assertDontSee('id="formCheckIn"', false);
    }

    public function test_friday_night_shift_checks_out_on_saturday(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'karyawan']);
        Employee::create([
            'user_id' => $user->id,
            'role_id' => Role::firstOrCreate(['name' => 'Satpam'])->id,
            'status' => 'aktif',
        ]);
        OfficeSetting::getActive()->update(['radius_meters' => 0]);
        $photoPayload = [
            'latitude' => -2.1234,
            'longitude' => 106.1234,
            'photo' => 'data:image/jpeg;base64,c2FtcGxl',
        ];

        $this->travelTo(Carbon::parse('2026-11-27 19:00', Attendance::TIMEZONE));
        $this->actingAs($user)->postJson('/attendance/checkin', $photoPayload + ['shift' => 'malam'])
            ->assertOk()->assertJson(['success' => true]);

        $this->travelTo(Carbon::parse('2026-11-28 07:00', Attendance::TIMEZONE));
        $this->get('/attendance')->assertOk()
            ->assertSee('Melanjutkan Absen Pulang Shift Malam Kemarin')
            ->assertSee('id="formCheckOut"', false)
            ->assertDontSee('id="formCheckIn"', false);
        $this->postJson('/attendance/checkout', $photoPayload + [
            'monev_activity' => 'Patroli area kantor dan pemeriksaan pintu.',
            'monev_obstacle' => str_repeat('Tidak ada kendala selama patroli. ', 4),
        ])->assertOk()->assertJson(['success' => true]);

        $attendance = Attendance::where('user_id', $user->id)->sole();
        $this->assertSame('2026-11-27', $attendance->date->toDateString());
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'shift' => 'malam',
            'check_in_time' => '19:00:00',
            'check_in_status' => 'tepat_waktu',
            'check_out_time' => '07:00:00',
        ]);
    }
}
