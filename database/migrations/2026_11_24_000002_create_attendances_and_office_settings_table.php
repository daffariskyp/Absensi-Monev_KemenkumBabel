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
        if (!Schema::hasTable('office_settings')) {
            Schema::create('office_settings', function (Blueprint $table) {
                $table->id();
                $table->string('office_name')->default('Kantor Utama Non-ASN');
                $table->decimal('latitude', 10, 7)->default(-6.1753924);
                $table->decimal('longitude', 10, 7)->default(106.8271528);
                $table->integer('radius_meters')->default(250); // Maksimal jarak dari kantor dalam meter
                $table->time('check_in_start')->default('06:00:00');
                $table->time('check_in_end')->default('08:30:00'); // Batas toleransi tepat waktu
                $table->time('check_out_start')->default('16:00:00');
                $table->timestamps();
            });

            // Insert default office settings (Kanwil Kemenkumham Kepulauan Bangka Belitung)
            DB::table('office_settings')->insert([
                'office_name' => 'Kanwil Kemenkumham Kepulauan Bangka Belitung',
                'latitude' => -2.1516954,
                'longitude' => 106.1568149,
                'radius_meters' => 350,
                'check_in_start' => '06:00:00',
                'check_in_end' => '08:30:00',
                'check_out_start' => '16:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->unsignedBigInteger('employee_id')->nullable();
                $table->date('date')->index();

                // Check-in (Masuk)
                $table->time('check_in_time')->nullable();
                $table->string('check_in_photo')->nullable(); // Foto wajah saat masuk
                $table->decimal('check_in_lat', 10, 7)->nullable();
                $table->decimal('check_in_lng', 10, 7)->nullable();
                $table->integer('check_in_distance')->nullable(); // Jarak meter dari titik kantor
                $table->enum('check_in_status', ['tepat_waktu', 'terlambat'])->nullable();

                // Check-out (Pulang)
                $table->time('check_out_time')->nullable();
                $table->string('check_out_photo')->nullable(); // Foto wajah saat pulang
                $table->decimal('check_out_lat', 10, 7)->nullable();
                $table->decimal('check_out_lng', 10, 7)->nullable();
                $table->integer('check_out_distance')->nullable();

                // Monev & Aktivitas Pulang
                $table->text('monev_activity')->nullable(); // Uraian aktivitas pekerjaan harian
                $table->text('monev_obstacle')->nullable(); // Kendala yang dihadapi (minimal 100 karakter)
                $table->text('activity_photos')->nullable(); // JSON path 1-3 foto kegiatan

                // Status Kehadiran
                $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'])->default('hadir');
                $table->text('notes')->nullable();

                $table->timestamps();

                $table->unique(['user_id', 'date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('office_settings');
    }
};
