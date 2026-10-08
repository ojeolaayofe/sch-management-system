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
        Schema::create('student_class_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->restrictOnDelete();
            $table->foreignId('class_id')->constrained('classes')->restrictOnDelete();
            $table->foreignId('class_arm_id')->nullable()->constrained('class_arms')->nullOnDelete();
            $table->enum('status', ['active', 'completed', 'transferred'])->default('active');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['student_id', 'academic_session_id']);
            $table->index(['class_id', 'academic_session_id']);
            $table->index(['class_arm_id', 'academic_session_id']);
            
            // Unique constraint: one student can only be in one class per session
            $table->unique(['student_id', 'academic_session_id', 'class_id'], 'unique_student_session_class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_class_assignments');
    }
};
