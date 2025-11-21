<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disciplinary_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('entity_type'); // player, club, referee, official
            $table->unsignedBigInteger('entity_id');
            $table->string('charge_type'); // violent_conduct, unsporting_behavior, match_fixing, admin_offense
            $table->enum('status', ['open', 'investigating', 'hearing_scheduled', 'decided', 'closed', 'appealed'])->default('open');
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->date('incident_date')->nullable();
            $table->foreignId('match_id')->nullable()->constrained()->nullOnDelete();
            $table->datetime('hearing_date')->nullable();
            $table->string('hearing_venue')->nullable();
            $table->text('decision')->nullable();
            $table->datetime('decision_date')->nullable();
            $table->json('evidence')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index('status');
        });

        Schema::create('disciplinary_sanctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('disciplinary_cases')->cascadeOnDelete();
            $table->enum('sanction_type', ['warning', 'fine', 'suspension', 'match_ban', 'points_deduction', 'disqualification', 'relegation', 'badge_revocation']);
            $table->text('description');
            $table->decimal('fine_amount_usd', 10, 2)->nullable();
            $table->integer('suspension_matches')->nullable();
            $table->date('suspension_start')->nullable();
            $table->date('suspension_end')->nullable();
            $table->integer('points_deducted')->nullable();
            $table->enum('status', ['pending', 'active', 'served', 'appealed', 'cancelled'])->default('pending');
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('disciplinary_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('disciplinary_cases')->cascadeOnDelete();
            $table->string('type'); // evidence, statement, decision, appeal
            $table->string('file_url');
            $table->string('file_name')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('appeals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('disciplinary_cases')->cascadeOnDelete();
            $table->text('grounds');
            $table->enum('status', ['submitted', 'under_review', 'hearing_scheduled', 'upheld', 'dismissed', 'modified'])->default('submitted');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->datetime('hearing_date')->nullable();
            $table->text('decision')->nullable();
            $table->datetime('decision_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeals');
        Schema::dropIfExists('disciplinary_documents');
        Schema::dropIfExists('disciplinary_sanctions');
        Schema::dropIfExists('disciplinary_cases');
    }
};
