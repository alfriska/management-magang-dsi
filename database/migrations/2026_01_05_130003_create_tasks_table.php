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
        // Task Assignments (bulk assignments to multiple interns)
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->time('deadline_time')->nullable();
            $table->boolean('assign_to_all')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('assigned_by');
            $table->index('priority');
            $table->index('deadline');
        });

        // Pivot table for task assignments to interns
        Schema::create('task_assignment_interns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('intern_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['task_assignment_id', 'intern_id']);
        });

        // Individual tasks for each intern
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_assignment_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('intern_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'scheduled', 'in_progress', 'submitted', 'revision', 'completed'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->time('deadline_time')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->datetime('completed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('is_late')->default(false);
            $table->text('submission_notes')->nullable();
            $table->json('submission_links')->nullable();
            $table->integer('score')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('priority');
            $table->index('assigned_by');
            $table->index(['intern_id', 'status']);
            $table->index(['assigned_by', 'created_at']);
            $table->index(['status', 'deadline']);
            $table->index(['intern_id', 'is_late']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_assignment_interns');
        Schema::dropIfExists('task_assignments');
    }
};
