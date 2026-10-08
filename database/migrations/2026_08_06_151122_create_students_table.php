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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique()->comment('Unique student/admission identifier');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('passport_photo')->nullable()->comment('Path to passport photograph');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->date('admission_date');
            $table->enum('status', ['active', 'inactive', 'transferred', 'graduated'])->default('active');
            
            // Parent/Guardian Information
            $table->string('parent_guardian_name')->nullable();
            $table->string('parent_guardian_phone')->nullable();
            $table->string('parent_guardian_email')->nullable();
            $table->string('parent_guardian_relationship')->nullable()->comment('e.g., father, mother, guardian');
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('student_id');
            $table->index('status');
            $table->index('admission_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
