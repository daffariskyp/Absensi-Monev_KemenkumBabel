<?php

namespace Tests\Unit;

use App\Models\Employee;
use App\Models\Role;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class EmployeeWorkDayTest extends TestCase
{
    private function employeeWithRole(?string $roleName): Employee
    {
        $employee = new Employee();

        if ($roleName !== null) {
            $employee->setRelation('role', new Role(['name' => $roleName]));
        }

        return $employee;
    }

    public function test_satpam_works_monday_until_friday(): void
    {
        $satpam = $this->employeeWithRole('Satpam');

        // 23 - 27 November 2026 = Senin s/d Jumat
        foreach (range(23, 27) as $day) {
            $this->assertTrue(
                $satpam->isWorkDay(Carbon::parse("2026-11-{$day}")),
                "Satpam seharusnya bekerja pada 2026-11-{$day}."
            );
        }

        $this->assertFalse($satpam->isWorkDay(Carbon::parse('2026-11-28')), 'Sabtu libur untuk Satpam.');
        $this->assertFalse($satpam->isWorkDay(Carbon::parse('2026-11-29')), 'Minggu libur untuk Satpam.');
    }

    public function test_other_non_asn_works_monday_until_thursday(): void
    {
        foreach (['Driver', 'Petugas Kebersihan', null] as $roleName) {
            $employee = $this->employeeWithRole($roleName);

            // 23 - 26 November 2026 = Senin s/d Kamis
            foreach (range(23, 26) as $day) {
                $this->assertTrue(
                    $employee->isWorkDay(Carbon::parse("2026-11-{$day}")),
                    'Non-ASN seharusnya bekerja pada 2026-11-' . $day . '.'
                );
            }

            $this->assertFalse($employee->isWorkDay(Carbon::parse('2026-11-27')), 'Jumat libur untuk Non-ASN.');
            $this->assertFalse($employee->isWorkDay(Carbon::parse('2026-11-28')), 'Sabtu libur untuk Non-ASN.');
            $this->assertFalse($employee->isWorkDay(Carbon::parse('2026-11-29')), 'Minggu libur untuk Non-ASN.');
        }
    }

    public function test_work_days_label_follows_role(): void
    {
        $this->assertSame('Senin - Jumat', $this->employeeWithRole('Satpam')->workDaysLabel());
        $this->assertSame('Senin - Kamis', $this->employeeWithRole('Driver')->workDaysLabel());
    }
}
