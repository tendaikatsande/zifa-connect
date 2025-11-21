<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->string('zifa_id')->unique()->nullable();
            $table->string('role'); // coach, medic, team_manager, physiotherapist
            $table->string('license_level')->nullable(); // CAF D, C, B, A, Pro
            $table->date('license_expiry')->nullable();
            $table->string('license_file_url')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('qualifications')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('role');
            $table->index('status');
        });

        Schema::create('referees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('zifa_id')->unique()->nullable();
            $table->enum('category', ['FIFA', 'Premier', 'Division_One', 'Amateur'])->default('Amateur');
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->date('fitness_test_expiry')->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('license_file_url')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->integer('matches_officiated')->default(0);
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
            $table->index('status');
        });

        Schema::create('training_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('provider')->nullable();
            $table->string('type'); // coaching, referee, medical, admin
            $table->string('level')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('venue')->nullable();
            $table->integer('capacity')->nullable();
            $table->decimal('fee_usd', 10, 2)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('course_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('training_courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('attended')->default(false);
            $table->boolean('passed')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('certificate_url')->nullable();
            $table->date('certificate_expiry')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_attendance');
        Schema::dropIfExists('training_courses');
        Schema::dropIfExists('referees');
        Schema::dropIfExists('officials');
    }
};
