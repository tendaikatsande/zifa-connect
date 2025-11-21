<?php

namespace App\Services;

use App\Models\Region;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    // Cache TTLs in seconds
    private const STATIC_TTL = 86400;      // 24 hours
    private const COMPUTED_TTL = 900;      // 15 minutes
    private const USER_TTL = 300;          // 5 minutes

    /**
     * Get all regions (cached for 24 hours)
     */
    public function getRegions(): array
    {
        return Cache::remember('regions', self::STATIC_TTL, function () {
            return Region::orderBy('name')->get()->toArray();
        });
    }

    /**
     * Get system settings (cached for 24 hours)
     */
    public function getSettings(): array
    {
        return Cache::remember('system_settings', self::STATIC_TTL, function () {
            return SystemSetting::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get a specific setting
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->getSettings();
        return $settings[$key] ?? $default;
    }

    /**
     * Get dashboard statistics (cached for 15 minutes)
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('dashboard_stats', self::COMPUTED_TTL, function () {
            return [
                'total_players' => \App\Models\Player::count(),
                'active_clubs' => \App\Models\Club::where('status', 'active')->count(),
                'pending_registrations' => \App\Models\Registration::where('status', 'pending_review')->count(),
                'pending_transfers' => \App\Models\Transfer::whereIn('status', ['pending_from_club', 'pending_zifa_review'])->count(),
                'total_revenue' => \App\Models\Payment::where('status', 'paid')->sum('amount_cents'),
                'outstanding_invoices' => \App\Models\Invoice::where('status', 'pending')->sum('amount_cents'),
            ];
        });
    }

    /**
     * Get competition standings (cached for 15 minutes)
     */
    public function getCompetitionStandings(int $competitionId): array
    {
        return Cache::remember("competition_{$competitionId}_standings", self::COMPUTED_TTL, function () use ($competitionId) {
            return \App\Models\CompetitionTeam::where('competition_id', $competitionId)
                ->with('club')
                ->orderBy('points', 'desc')
                ->orderBy('goal_difference', 'desc')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get user permissions (cached for 5 minutes)
     */
    public function getUserPermissions(int $userId): array
    {
        return Cache::remember("user_{$userId}_permissions", self::USER_TTL, function () use ($userId) {
            $user = \App\Models\User::find($userId);
            if (!$user) {
                return [];
            }

            $permissions = $user->permissions->pluck('name')->toArray();

            // Add permissions from roles
            foreach ($user->roles as $role) {
                $rolePermissions = $role->permissions->pluck('name')->toArray();
                $permissions = array_merge($permissions, $rolePermissions);
            }

            return array_unique($permissions);
        });
    }

    /**
     * Clear user permission cache
     */
    public function clearUserPermissions(int $userId): void
    {
        Cache::forget("user_{$userId}_permissions");
    }

    /**
     * Clear settings cache
     */
    public function clearSettings(): void
    {
        Cache::forget('system_settings');
    }

    /**
     * Clear dashboard stats cache
     */
    public function clearDashboardStats(): void
    {
        Cache::forget('dashboard_stats');
    }

    /**
     * Clear competition standings cache
     */
    public function clearCompetitionStandings(int $competitionId): void
    {
        Cache::forget("competition_{$competitionId}_standings");
    }

    /**
     * Clear all caches
     */
    public function clearAll(): void
    {
        Cache::flush();
    }
}
