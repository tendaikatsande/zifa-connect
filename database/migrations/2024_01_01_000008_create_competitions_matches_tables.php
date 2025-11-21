<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->enum('type', ['league', 'cup', 'tournament', 'friendly'])->default('league');
            $table->string('season');
            $table->foreignId('region_id')->nullable()->constrained()->nullOnDelete();
            $table->string('age_group')->nullable(); // senior, u20, u17, women
            $table->enum('status', ['planned', 'registration', 'active', 'completed', 'cancelled'])->default('planned');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('max_teams')->nullable();
            $table->decimal('entry_fee_usd', 10, 2)->nullable();
            $table->json('rules')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('season');
            $table->index('status');
        });

        Schema::create('competition_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['registered', 'confirmed', 'withdrawn', 'disqualified'])->default('registered');
            $table->integer('points')->default(0);
            $table->integer('played')->default(0);
            $table->integer('won')->default(0);
            $table->integer('drawn')->default(0);
            $table->integer('lost')->default(0);
            $table->integer('goals_for')->default(0);
            $table->integer('goals_against')->default(0);
            $table->integer('goal_difference')->default(0);
            $table->integer('position')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['competition_id', 'club_id']);
        });

        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->string('match_number')->nullable();
            $table->foreignId('competition_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('home_club_id')->constrained('clubs')->cascadeOnDelete();
            $table->foreignId('away_club_id')->constrained('clubs')->cascadeOnDelete();
            $table->string('venue')->nullable();
            $table->dateTime('match_date');
            $table->enum('status', ['scheduled', 'postponed', 'in_progress', 'finished', 'abandoned', 'cancelled'])->default('scheduled');
            $table->integer('score_home')->nullable();
            $table->integer('score_away')->nullable();
            $table->integer('ht_score_home')->nullable();
            $table->integer('ht_score_away')->nullable();
            $table->foreignId('referee_id')->nullable()->constrained('referees')->nullOnDelete();
            $table->foreignId('assistant_referee_1_id')->nullable()->constrained('referees')->nullOnDelete();
            $table->foreignId('assistant_referee_2_id')->nullable()->constrained('referees')->nullOnDelete();
            $table->foreignId('fourth_official_id')->nullable()->constrained('referees')->nullOnDelete();
            $table->foreignId('match_commissioner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('report_submitted')->default(false);
            $table->string('report_url')->nullable();
            $table->integer('attendance')->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('competition_id');
            $table->index('match_date');
            $table->index('status');
        });

        Schema::create('match_squads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_starting')->default(false);
            $table->integer('shirt_number')->nullable();
            $table->string('position')->nullable();
            $table->boolean('is_captain')->default(false);
            $table->timestamps();

            $table->unique(['match_id', 'club_id', 'player_id']);
        });

        Schema::create('match_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('event_type', ['goal', 'own_goal', 'penalty_goal', 'penalty_miss', 'yellow_card', 'red_card', 'second_yellow', 'substitution_in', 'substitution_out', 'injury']);
            $table->integer('minute');
            $table->integer('added_time')->nullable();
            $table->foreignId('related_player_id')->nullable()->constrained('players')->nullOnDelete(); // assist or substituted player
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['match_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_events');
        Schema::dropIfExists('match_squads');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('competition_teams');
        Schema::dropIfExists('competitions');
    }
};
