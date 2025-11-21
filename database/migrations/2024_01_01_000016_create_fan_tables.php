<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fan profiles - extends user with fan-specific data
        Schema::create('fan_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nickname')->nullable();
            $table->foreignId('favorite_club_id')->nullable()->constrained('clubs')->nullOnDelete();
            $table->foreignId('favorite_player_id')->nullable()->constrained('players')->nullOnDelete();
            $table->string('city')->nullable();
            $table->date('member_since')->nullable();
            $table->integer('loyalty_points')->default(0);
            $table->enum('membership_tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
            $table->json('preferences')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('user_id');
            $table->index('favorite_club_id');
            $table->index('membership_tier');
        });

        // Club follows - fans following clubs
        Schema::create('club_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('club_id')->constrained()->onDelete('cascade');
            $table->boolean('notifications_enabled')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'club_id']);
            $table->index('club_id');
        });

        // Player follows - fans following players
        Schema::create('player_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->boolean('notifications_enabled')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'player_id']);
            $table->index('player_id');
        });

        // Match attendance - tracking fan attendance at matches
        Schema::create('match_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('match_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['registered', 'checked_in', 'attended', 'cancelled'])->default('registered');
            $table->string('ticket_reference')->nullable();
            $table->string('seat_section')->nullable();
            $table->integer('loyalty_points_earned')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'match_id']);
            $table->index('match_id');
            $table->index('status');
        });

        // Fan polls - voting for player of the match, etc.
        Schema::create('fan_polls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['player_of_match', 'goal_of_week', 'best_player', 'custom'])->default('custom');
            $table->foreignId('match_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('competition_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft', 'active', 'closed', 'archived'])->default('draft');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('type');
            $table->index(['starts_at', 'ends_at']);
        });

        // Poll options - choices for each poll
        Schema::create('fan_poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fan_poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_option')->nullable();
            $table->string('image_url')->nullable();
            $table->integer('votes_count')->default(0);
            $table->timestamps();

            $table->index('fan_poll_id');
        });

        // Poll votes - fan votes on polls
        Schema::create('fan_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fan_poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('fan_poll_option_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['fan_poll_id', 'user_id']);
            $table->index('fan_poll_option_id');
        });

        // Fan news - announcements and news for fans
        Schema::create('fan_news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->enum('category', ['announcement', 'match_preview', 'match_report', 'transfer', 'interview', 'general'])->default('general');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('match_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->integer('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('category');
            $table->index('published_at');
            $table->index('is_featured');
        });

        // Fan comments on news
        Schema::create('fan_news_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fan_news_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('fan_news_comments')->nullOnDelete();
            $table->text('content');
            $table->enum('status', ['pending', 'approved', 'rejected', 'flagged'])->default('pending');
            $table->integer('likes_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('fan_news_id');
            $table->index('status');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fan_news_comments');
        Schema::dropIfExists('fan_news');
        Schema::dropIfExists('fan_poll_votes');
        Schema::dropIfExists('fan_poll_options');
        Schema::dropIfExists('fan_polls');
        Schema::dropIfExists('match_attendances');
        Schema::dropIfExists('player_follows');
        Schema::dropIfExists('club_follows');
        Schema::dropIfExists('fan_profiles');
    }
};
