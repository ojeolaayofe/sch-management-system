<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examination_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->foreignId('class_id')->constrained('classes')->restrictOnDelete();
            $table->foreignId('class_arm_id')->nullable()->constrained('class_arms')->nullOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->restrictOnDelete();
            $table->foreignId('academic_term_id')->nullable()->constrained('academic_terms')->nullOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->decimal('max_score', 5, 2)->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'subject_id', 'academic_session_id', 'academic_term_id']);
            $table->index(['teacher_id', 'class_id']);
            
            // Unique constraint
            $table->unique(['student_id', 'subject_id', 'academic_session_id', 'academic_term_id'], 'unique_student_subject_exam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examination_scores');
    }
};
