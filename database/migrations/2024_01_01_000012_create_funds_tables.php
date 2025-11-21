<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // development, youth, women, emergency, infrastructure
            $table->decimal('total_amount_cents', 15, 0)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['active', 'closed', 'frozen'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('fund_disbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount_cents', 15, 0);
            $table->string('purpose');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'disbursed', 'rejected', 'acquitted'])->default('pending');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            $table->string('acquittal_doc_url')->nullable();
            $table->timestamp('acquitted_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->string('sponsor_name');
            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contract_url')->nullable();
            $table->decimal('amount_cents', 15, 0)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('type'); // title, kit, broadcast, stadium
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['negotiating', 'active', 'expired', 'terminated'])->default('negotiating');
            $table->json('deliverables')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorships');
        Schema::dropIfExists('fund_disbursements');
        Schema::dropIfExists('funds');
    }
};
