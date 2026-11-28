<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

        Schema::table('administrations', function (Blueprint $table) {
            $table->string('leave_category', 100)->change();
        });
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
