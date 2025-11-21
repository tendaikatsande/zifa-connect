<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_reference')->unique();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('to_club_id')->constrained('clubs')->cascadeOnDelete();
            $table->enum('type', ['local', 'international', 'loan', 'loan_return', 'free'])->default('local');
            $table->string('fifa_tms_id')->nullable();
            $table->string('transfer_window')->nullable(); // 2024_summer, 2024_winter
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['requested', 'pending_from_club', 'pending_payment', 'pending_zifa_review', 'approved', 'rejected', 'cancelled', 'completed'])->default('requested');
            $table->decimal('transfer_fee_usd', 12, 2)->nullable();
            $table->decimal('admin_fee_usd', 10, 2)->nullable();
            $table->foreignId('from_club_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('from_club_approved_at')->nullable();
            $table->foreignId('zifa_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('zifa_approved_at')->nullable();
            $table->date('effective_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('certificate_url')->nullable(); // Local Transfer Certificate
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('player_id');
            $table->index('status');
            $table->index('type');
        });

        Schema::create('transfer_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // release_letter, ITC, contract, clearance, payment_proof
            $table->string('file_url');
            $table->string('file_name')->nullable();
            $table->timestamps();
        });

        Schema::create('transfer_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->date('joined_date');
            $table->date('left_date')->nullable();
            $table->string('transfer_type')->nullable();
            $table->foreignId('transfer_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['player_id', 'joined_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_history');
        Schema::dropIfExists('transfer_documents');
        Schema::dropIfExists('transfers');
    }
};
