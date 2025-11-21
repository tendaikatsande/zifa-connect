<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Player indexes
        Schema::table('players', function (Blueprint $table) {
            $table->index(['current_club_id', 'status'], 'idx_players_club_status');
            $table->index(['status', 'created_at'], 'idx_players_status_created');
            $table->index('zifa_id', 'idx_players_zifa_id');
            $table->index('registration_category', 'idx_players_category');
        });

        // Transfer indexes
        Schema::table('transfers', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_transfers_status_created');
            $table->index(['player_id', 'status'], 'idx_transfers_player_status');
            $table->index('from_club_id', 'idx_transfers_from_club');
            $table->index('to_club_id', 'idx_transfers_to_club');
        });

        // Invoice indexes
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['status', 'due_date'], 'idx_invoices_status_due');
            $table->index(['club_id', 'status'], 'idx_invoices_club_status');
            $table->index('category', 'idx_invoices_category');
        });

        // Payment indexes
        Schema::table('payments', function (Blueprint $table) {
            $table->index('gateway_reference', 'idx_payments_gateway_ref');
            $table->index(['status', 'paid_at'], 'idx_payments_status_paid');
            $table->index('invoice_id', 'idx_payments_invoice');
        });

        // Match indexes
        Schema::table('matches', function (Blueprint $table) {
            $table->index(['match_date', 'competition_id'], 'idx_matches_date_competition');
            $table->index(['status', 'match_date'], 'idx_matches_status_date');
            $table->index('home_club_id', 'idx_matches_home_club');
            $table->index('away_club_id', 'idx_matches_away_club');
        });

        // Registration indexes
        Schema::table('registrations', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_registrations_status_created');
            $table->index(['entity_type', 'entity_id'], 'idx_registrations_entity');
            $table->index('club_id', 'idx_registrations_club');
        });

        // Club indexes
        Schema::table('clubs', function (Blueprint $table) {
            $table->index(['status', 'category'], 'idx_clubs_status_category');
            $table->index('affiliation_status', 'idx_clubs_affiliation');
        });

        // Disciplinary case indexes
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_disciplinary_status_created');
            $table->index(['entity_type', 'entity_id'], 'idx_disciplinary_entity');
        });

        // FIFA sync queue indexes
        Schema::table('fifa_sync_queue', function (Blueprint $table) {
            $table->index(['status', 'scheduled_at'], 'idx_fifa_sync_status_scheduled');
            $table->index(['entity_type', 'entity_id'], 'idx_fifa_sync_entity');
        });

        // Activity log indexes (for audit queries)
        Schema::table('activity_log', function (Blueprint $table) {
            $table->index(['causer_type', 'causer_id'], 'idx_activity_causer');
            $table->index(['subject_type', 'subject_id'], 'idx_activity_subject');
            $table->index('created_at', 'idx_activity_created');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropIndex('idx_players_club_status');
            $table->dropIndex('idx_players_status_created');
            $table->dropIndex('idx_players_zifa_id');
            $table->dropIndex('idx_players_category');
        });

        Schema::table('transfers', function (Blueprint $table) {
            $table->dropIndex('idx_transfers_status_created');
            $table->dropIndex('idx_transfers_player_status');
            $table->dropIndex('idx_transfers_from_club');
            $table->dropIndex('idx_transfers_to_club');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_status_due');
            $table->dropIndex('idx_invoices_club_status');
            $table->dropIndex('idx_invoices_category');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_gateway_ref');
            $table->dropIndex('idx_payments_status_paid');
            $table->dropIndex('idx_payments_invoice');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex('idx_matches_date_competition');
            $table->dropIndex('idx_matches_status_date');
            $table->dropIndex('idx_matches_home_club');
            $table->dropIndex('idx_matches_away_club');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex('idx_registrations_status_created');
            $table->dropIndex('idx_registrations_entity');
            $table->dropIndex('idx_registrations_club');
        });

        Schema::table('clubs', function (Blueprint $table) {
            $table->dropIndex('idx_clubs_status_category');
            $table->dropIndex('idx_clubs_affiliation');
        });

        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->dropIndex('idx_disciplinary_status_created');
            $table->dropIndex('idx_disciplinary_entity');
        });

        Schema::table('fifa_sync_queue', function (Blueprint $table) {
            $table->dropIndex('idx_fifa_sync_status_scheduled');
            $table->dropIndex('idx_fifa_sync_entity');
        });

        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex('idx_activity_causer');
            $table->dropIndex('idx_activity_subject');
            $table->dropIndex('idx_activity_created');
        });
    }
};
