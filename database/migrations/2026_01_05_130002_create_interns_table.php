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
        Schema::create('interns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nis')->nullable(); // Nomor Induk Siswa
            $table->string('school'); // Asal sekolah
            $table->string('department'); // Jurusan
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('active');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('certificate_number')->nullable()->unique();
            $table->date('certificate_issued_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('supervisor_id');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interns');
    }
};
