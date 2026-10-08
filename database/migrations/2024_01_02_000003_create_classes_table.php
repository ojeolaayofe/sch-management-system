<?php

/**
 * Create classes table
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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g., JSS 1, SS 2, Primary 5');
            $table->string('code')->comment('e.g., JSS1, SS2, P5');
            $table->enum('section', ['nursery', 'primary', 'junior_secondary', 'senior_secondary']);
            $table->integer('display_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique('code');
            $table->index('section');
            $table->index('status');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
