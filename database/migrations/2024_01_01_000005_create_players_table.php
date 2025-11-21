<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('zifa_id')->unique()->nullable();
            $table->string('fifa_connect_id')->unique()->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_names')->nullable();
            $table->date('dob');
            $table->string('place_of_birth')->nullable();
            $table->enum('gender', ['M', 'F', 'Other']);
            $table->string('nationality')->default('Zimbabwean');
            $table->integer('height_cm')->nullable();
            $table->integer('weight_kg')->nullable();
            $table->enum('dominant_foot', ['left', 'right', 'both'])->nullable();
            $table->string('marital_status')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('photo_url')->nullable();
            $table->foreignId('current_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'suspended', 'retired', 'free_agent'])->default('draft');
            $table->string('registration_category')->nullable(); // senior, u20, u17, futsal, women
            $table->string('primary_position')->nullable();
            $table->string('secondary_position')->nullable();
            $table->string('national_id')->nullable();
            $table->string('passport_number')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['last_name', 'first_name']);
            $table->index('zifa_id');
            $table->index('fifa_connect_id');
            $table->index('current_club_id');
            $table->index('status');
        });

        Schema::create('player_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // birth_cert, passport, photo, medical, contract, national_id
            $table->string('file_url');
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->boolean('verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->date('expiry_date')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['player_id', 'type']);
        });

        Schema::create('player_medicals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->string('doctor_name')->nullable();
            $table->string('clinic')->nullable();
            $table->enum('medical_result', ['fit', 'unfit', 'conditional'])->default('fit');
            $table->text('notes')->nullable();
            $table->date('examination_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('certificate_url')->nullable();
            $table->timestamps();

            $table->index('player_id');
        });

        Schema::create('player_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('contract_file_url')->nullable();
            $table->decimal('salary_usd', 12, 2)->nullable();
            $table->decimal('signing_fee_usd', 12, 2)->nullable();
            $table->decimal('release_clause_usd', 12, 2)->nullable();
            $table->enum('status', ['active', 'terminated', 'expired', 'suspended'])->default('active');
            $table->text('terms')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['player_id', 'status']);
            $table->index(['club_id', 'status']);
        });

        Schema::create('player_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->string('season');
            $table->foreignId('competition_id')->nullable();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('matches_played')->default(0);
            $table->integer('matches_started')->default(0);
            $table->integer('goals')->default(0);
            $table->integer('assists')->default(0);
            $table->integer('yellow_cards')->default(0);
            $table->integer('red_cards')->default(0);
            $table->integer('minutes_played')->default(0);
            $table->integer('clean_sheets')->default(0); // for goalkeepers
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['player_id', 'season', 'competition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_statistics');
        Schema::dropIfExists('player_contracts');
        Schema::dropIfExists('player_medicals');
        Schema::dropIfExists('player_documents');
        Schema::dropIfExists('players');
    }
};
