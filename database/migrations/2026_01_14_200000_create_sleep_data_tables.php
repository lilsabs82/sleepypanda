<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User profiles with additional info
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });

        // Sleep records - daily sleep entries
        Schema::create('sleep_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('sleep_date');
            $table->time('sleep_start'); // When user started sleeping
            $table->time('sleep_end');   // When user woke up
            $table->integer('duration_minutes'); // Total sleep duration
            $table->integer('quality_percent')->default(0); // Sleep quality 0-100
            $table->boolean('is_insomnia')->default(false);
            $table->integer('time_to_sleep_minutes')->nullable(); // Time taken to fall asleep
            $table->timestamps();

            $table->index(['user_id', 'sleep_date']);
            $table->index('sleep_date');
        });

        // Insomnia alerts
        Schema::create('insomnia_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('hours_without_sleep');
            $table->integer('avg_duration_minutes');
            $table->timestamp('alert_date');
            $table->timestamps();

            $table->index('alert_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insomnia_alerts');
        Schema::dropIfExists('sleep_records');
        Schema::dropIfExists('user_profiles');
    }
};
