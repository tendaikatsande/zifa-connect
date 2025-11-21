<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('registration_number')->unique()->nullable();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('affiliated_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('logo_url')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'suspended'])->default('pending');
            $table->date('registration_date')->nullable();
            $table->date('license_expiry')->nullable();
            $table->json('age_groups')->nullable(); // u10, u12, u14, u16
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stadiums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('capacity')->nullable();
            $table->enum('surface', ['natural_grass', 'artificial', 'hybrid'])->nullable();
            $table->boolean('has_floodlights')->default(false);
            $table->boolean('has_var')->default(false);
            $table->enum('status', ['operational', 'under_maintenance', 'closed'])->default('operational');
            $table->string('license_grade')->nullable(); // CAF Grade A, B, C
            $table->date('license_expiry')->nullable();
            $table->json('facilities')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('zifa_id')->unique()->nullable();
            $table->string('fifa_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_expiry')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('agent_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->enum('status', ['active', 'expired', 'terminated'])->default('active');
            $table->timestamps();

            $table->unique(['agent_id', 'player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_players');
        Schema::dropIfExists('agents');
        Schema::dropIfExists('stadiums');
        Schema::dropIfExists('academies');
    }
};
