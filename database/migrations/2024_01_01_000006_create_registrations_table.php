<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('entity_type'); // player, club, official, referee, academy
            $table->unsignedBigInteger('entity_id');
            $table->string('season');
            $table->enum('status', ['pending_payment', 'pending_review', 'approved', 'rejected', 'expired'])->default('pending_payment');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('status');
            $table->index('season');
        });

        Schema::create('affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->string('season');
            $table->enum('status', ['pending', 'active', 'expired', 'suspended'])->default('pending');
            $table->date('expiry_date')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'partial', 'overdue'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['club_id', 'season']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliations');
        Schema::dropIfExists('registrations');
    }
};
