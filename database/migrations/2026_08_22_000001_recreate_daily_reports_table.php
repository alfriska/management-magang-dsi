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
        // Tabel lama masih kosong (belum ada data intern yang mengisi),
        // aman di-drop & dibuat ulang dengan struktur final.
        Schema::dropIfExists('daily_reports');

        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->text('description');
            $table->string('image')->nullable();
            $table->date('target_date')->nullable();
            $table->enum('status', ['belum_dikerjakan', 'on_progress', 'selesai'])
                ->default('belum_dikerjakan');
            $table->timestamps();

            $table->unique(['intern_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};