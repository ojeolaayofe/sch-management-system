<?php

/**
 * Create class arms table
 *
 * @package SchoolHub\Database\Migrations
 */

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
        Schema::create('class_arms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->restrictOnDelete();
            $table->string('arm_name')->comment('e.g., A, B, C');
            $table->integer('capacity')->default(50);
            $table->foreignId('class_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['class_id', 'arm_name']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_arms');
    }
};
