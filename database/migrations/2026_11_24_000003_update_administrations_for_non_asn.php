<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('administrations', function (Blueprint $table) {
            if (!Schema::hasColumn('administrations', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('contacted');
            }
            if (!Schema::hasColumn('administrations', 'approver_id')) {
                $table->unsignedBigInteger('approver_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('administrations', 'approver_note')) {
                $table->text('approver_note')->nullable()->after('approver_id');
            }
            if (!Schema::hasColumn('administrations', 'attachment')) {
                $table->string('attachment')->nullable()->after('approver_note');
            }
        });

        // Modify leave_category column to be varchar so custom non-asn categories can be stored safely
        // In MySQL, we can alter the column or keep string
        try {
            DB::statement("ALTER TABLE administrations MODIFY COLUMN leave_category VARCHAR(100) NOT NULL");
        } catch (\Exception $e) {
            // SQLite or already string
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('administrations', function (Blueprint $table) {
            if (Schema::hasColumn('administrations', 'status')) {
                $table->dropColumn(['status', 'approver_id', 'approver_note', 'attachment']);
            }
        });
    }
};
