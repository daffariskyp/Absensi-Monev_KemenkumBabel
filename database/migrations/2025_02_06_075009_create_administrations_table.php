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
        Schema::create('administrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Untuk mereferensikan pengguna
            $table->enum('leave_category', [
                'cuti_hari_raya',
                'cuti_sakit',
                'cuti_tahunan',
                'cuti_menikah',
                'cuti_melahirkan',
                'cuti_keguguran',
                'cuti_berduka',
            ]); // Kategori cuti dengan nilai yang sudah ditentukan
            $table->string('lama_hari_cuti'); // lamanya cuti yang di ambil
            $table->date('start_date'); // Tanggal mulai
            $table->date('end_date'); // Tanggal akhir
            $table->text('description'); // Deskripsi alasan cuti
            $table->boolean('bring_laptop'); // Membawa laptop
            $table->boolean('contacted'); // Masih dapat dihubungi
            $table->timestamps();
        
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrations');
    }
};
