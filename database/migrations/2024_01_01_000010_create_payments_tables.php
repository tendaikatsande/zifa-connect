<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->string('entity_type'); // player, club, official, transfer
            $table->unsignedBigInteger('entity_id');
            $table->string('description');
            $table->string('category'); // registration, affiliation, transfer, fine, course, competition_entry
            $table->decimal('amount_cents', 15, 0);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['draft', 'sent', 'pending', 'paid', 'partial', 'overdue', 'cancelled', 'refunded'])->default('draft');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->foreignId('issued_to_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('issued_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('line_items')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('invoice_number');
            $table->index(['entity_type', 'entity_id']);
            $table->index('status');
            $table->index('due_date');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('payment_reference')->unique();
            $table->decimal('amount_cents', 15, 0);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['initiated', 'pending', 'processing', 'paid', 'failed', 'refunded', 'chargeback'])->default('initiated');
            $table->enum('gateway', ['pesepay', 'bank_transfer', 'cash', 'cheque', 'other'])->default('pesepay');
            $table->string('gateway_method')->nullable(); // ecocash, onemoney, visa, mastercard, zipit
            $table->string('gateway_reference')->unique()->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('initiated_at')->useCurrent();
            $table->timestamp('paid_at')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('callback_payload')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('invoice_id');
            $table->index('gateway_reference');
            $table->index('status');
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_cents', 15, 0);
            $table->string('reason');
            $table->enum('status', ['requested', 'processing', 'completed', 'failed'])->default('requested');
            $table->string('gateway_reference')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gateway_reference');
            $table->string('gateway_status');
            $table->json('gateway_payload')->nullable();
            $table->boolean('matched')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('reconciled_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_reconciliations');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
