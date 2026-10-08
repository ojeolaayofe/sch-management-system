<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('SchoolHub');
            $table->string('school_motto')->nullable();
            $table->string('school_email')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('school_logo')->nullable();
            $table->string('academic_session')->nullable();
            $table->string('current_term')->nullable();
            $table->string('timezone')->default('Africa/Lagos');
            $table->string('currency')->default('NGN');
            $table->string('primary_color')->default('#4f46e5');
            $table->string('secondary_color')->default('#0ea5e9');
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->default('tls');
            $table->timestamps();
        });

        // Insert default row
        DB::table('application_settings')->insert([
            'school_name' => 'SchoolHub',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
            'primary_color' => '#4f46e5',
            'secondary_color' => '#0ea5e9',
            'smtp_encryption' => 'tls',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('application_settings');
    }
};
