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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['weekly', 'monthly', 'final'])->default('weekly');
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->text('feedback')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('type');
            $table->index('status');
            $table->index(['intern_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
