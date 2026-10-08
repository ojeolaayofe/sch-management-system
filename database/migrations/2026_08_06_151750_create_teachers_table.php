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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Linked user account for login');
            $table->string('teacher_id')->unique()->comment('Unique teacher identifier');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('passport_photo')->nullable()->comment('Path to passport photograph');
            $table->string('qualification')->nullable()->comment('Highest qualification');
            $table->date('employment_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_leave', 'resigned'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('teacher_id');
            $table->index('status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
