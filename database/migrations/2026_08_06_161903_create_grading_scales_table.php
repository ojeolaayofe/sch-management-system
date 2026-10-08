<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grading_scales', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('e.g., Standard Grading');
            $table->decimal('min_score', 5, 2)->default(0);
            $table->decimal('max_score', 5, 2)->default(100);
            $table->string('grade', 2)->comment('e.g., A, B, C');
            $table->string('remark')->nullable()->comment('e.g., Excellent, Very Good');
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_scales');
    }
};
