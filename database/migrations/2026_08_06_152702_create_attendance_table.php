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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete()->comment('Teacher who marked attendance');
            $table->foreignId('class_id')->constrained('classes')->restrictOnDelete();
            $table->foreignId('class_arm_id')->nullable()->constrained('class_arms')->nullOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->restrictOnDelete();
            $table->foreignId('academic_term_id')->nullable()->constrained('academic_terms')->nullOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['student_id', 'attendance_date']);
            $table->index(['class_id', 'attendance_date']);
            $table->index(['class_arm_id', 'attendance_date']);
            $table->index(['academic_session_id', 'academic_term_id']);
            $table->index('attendance_date');
            
            // Unique constraint: one attendance record per student per day per class
            $table->unique(['student_id', 'class_id', 'attendance_date'], 'unique_student_class_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
