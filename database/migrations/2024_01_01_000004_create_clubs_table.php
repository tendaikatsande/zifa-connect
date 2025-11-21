<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('registration_number')->unique()->nullable();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('home_ground')->nullable();
            $table->string('logo_url')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'suspended', 'deregistered'])->default('pending');
            $table->date('registration_date')->nullable();
            $table->date('affiliation_expiry')->nullable();
            $table->integer('established_year')->nullable();
            $table->enum('category', ['premier', 'division_one', 'division_two', 'women', 'futsal', 'youth'])->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('status');
            $table->index('region_id');
        });

        Schema::create('club_officials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('position'); // chairman, secretary, treasurer, admin, medic
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['club_id', 'user_id', 'position']);
        });

        Schema::create('club_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // constitution, registration_certificate, proof_of_payment
            $table->string('file_url');
            $table->string('file_name')->nullable();
            $table->integer('file_size')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->date('expiry_date')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['club_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_documents');
        Schema::dropIfExists('club_officials');
        Schema::dropIfExists('clubs');
    }
};
