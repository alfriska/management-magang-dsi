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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained()->onDelete('cascade');
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assessed_by')->constrained('users')->onDelete('cascade');
            $table->integer('quality_score'); // 1-100
            $table->integer('speed_score'); // 1-100
            $table->integer('initiative_score'); // 1-100
            $table->integer('teamwork_score'); // 1-100
            $table->integer('communication_score'); // 1-100
            $table->text('strengths')->nullable();
            $table->text('improvements')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('task_id');
            $table->index('assessed_by');
            $table->index(['task_id', 'intern_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
