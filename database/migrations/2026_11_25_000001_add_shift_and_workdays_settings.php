<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('attendances') && !Schema::hasColumn('attendances', 'shift')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->enum('shift', ['pagi', 'malam'])->nullable()->after('employee_id');
            });
        }

        if (Schema::hasTable('office_settings')) {
            Schema::table('office_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('office_settings', 'satpam_pagi_in_start')) {
                    $table->time('satpam_pagi_in_start')->default('06:30:00')->after('check_out_start');
                    $table->time('satpam_pagi_in_end')->default('08:00:00')->after('satpam_pagi_in_start');
                    $table->time('satpam_pagi_out_start')->default('19:00:00')->after('satpam_pagi_in_end');

                    $table->time('satpam_malam_in_start')->default('18:30:00')->after('satpam_pagi_out_start');
                    $table->time('satpam_malam_in_end')->default('20:00:00')->after('satpam_malam_in_start');
                    $table->time('satpam_malam_out_start')->default('07:00:00')->after('satpam_malam_in_end');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendances') && Schema::hasColumn('attendances', 'shift')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('shift');
            });
        }

        if (Schema::hasTable('office_settings') && Schema::hasColumn('office_settings', 'satpam_pagi_in_start')) {
            Schema::table('office_settings', function (Blueprint $table) {
                $table->dropColumn([
                    'satpam_pagi_in_start',
                    'satpam_pagi_in_end',
                    'satpam_pagi_out_start',
                    'satpam_malam_in_start',
                    'satpam_malam_in_end',
                    'satpam_malam_out_start',
                ]);
            });
        }
    }
};
