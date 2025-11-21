<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FanProfile;
use App\Models\ClubFollow;
use App\Models\PlayerFollow;
use App\Models\MatchAttendance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FanProfileController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FanProfile::with(['user', 'favoriteClub', 'favoritePlayer'])
            ->when($request->membership_tier, fn($q, $tier) => $q->where('membership_tier', $tier))
            ->when($request->club_id, fn($q, $clubId) => $q->where('favorite_club_id', $clubId))
            ->orderBy($request->sort ?? 'loyalty_points', $request->order ?? 'desc');

        $profiles = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json($profiles);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nickname' => 'nullable|string|max:100',
            'favorite_club_id' => 'nullable|exists:clubs,id',
            'favorite_player_id' => 'nullable|exists:players,id',
            'city' => 'nullable|string|max:100',
            'preferences' => 'nullable|array',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['member_since'] = now();

        $profile = FanProfile::create($validated);

        return response()->json($profile->load(['favoriteClub', 'favoritePlayer']), 201);
    }

    public function show(Request $request): JsonResponse
    {
        $profile = FanProfile::where('user_id', $request->user()->id)
            ->with(['favoriteClub', 'favoritePlayer'])
            ->firstOrFail();

        return response()->json($profile);
    }

    public function update(Request $request): JsonResponse
    {
        $profile = FanProfile::where('user_id', $request->user()->id)->firstOrFail();

        $validated = $request->validate([
            'nickname' => 'nullable|string|max:100',
            'favorite_club_id' => 'nullable|exists:clubs,id',
            'favorite_player_id' => 'nullable|exists:players,id',
            'city' => 'nullable|string|max:100',
            'preferences' => 'nullable|array',
        ]);

        $profile->update($validated);

        return response()->json($profile->load(['favoriteClub', 'favoritePlayer']));
    }

    public function followClub(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'notifications_enabled' => 'boolean',
        ]);

        $follow = ClubFollow::updateOrCreate(
            ['user_id' => $request->user()->id, 'club_id' => $validated['club_id']],
            ['notifications_enabled' => $validated['notifications_enabled'] ?? true]
        );

        return response()->json($follow->load('club'), 201);
    }

    public function unfollowClub(Request $request, int $clubId): JsonResponse
    {
        ClubFollow::where('user_id', $request->user()->id)
            ->where('club_id', $clubId)
            ->delete();

        return response()->json(['message' => 'Unfollowed club successfully']);
    }

    public function followedClubs(Request $request): JsonResponse
    {
        $follows = ClubFollow::where('user_id', $request->user()->id)
            ->with('club')
            ->get();

        return response()->json($follows);
    }

    public function followPlayer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'notifications_enabled' => 'boolean',
        ]);

        $follow = PlayerFollow::updateOrCreate(
            ['user_id' => $request->user()->id, 'player_id' => $validated['player_id']],
            ['notifications_enabled' => $validated['notifications_enabled'] ?? true]
        );

        return response()->json($follow->load('player'), 201);
    }

    public function unfollowPlayer(Request $request, int $playerId): JsonResponse
    {
        PlayerFollow::where('user_id', $request->user()->id)
            ->where('player_id', $playerId)
            ->delete();

        return response()->json(['message' => 'Unfollowed player successfully']);
    }

    public function followedPlayers(Request $request): JsonResponse
    {
        $follows = PlayerFollow::where('user_id', $request->user()->id)
            ->with('player')
            ->get();

        return response()->json($follows);
    }

    public function registerAttendance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:matches,id',
            'ticket_reference' => 'nullable|string|max:100',
            'seat_section' => 'nullable|string|max:50',
        ]);

        $attendance = MatchAttendance::updateOrCreate(
            ['user_id' => $request->user()->id, 'match_id' => $validated['match_id']],
            [
                'ticket_reference' => $validated['ticket_reference'] ?? null,
                'seat_section' => $validated['seat_section'] ?? null,
                'status' => 'registered',
            ]
        );

        return response()->json($attendance->load('match'), 201);
    }

    public function myAttendances(Request $request): JsonResponse
    {
        $attendances = MatchAttendance::where('user_id', $request->user()->id)
            ->with('match')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($attendances);
    }

    public function leaderboard(Request $request): JsonResponse
    {
        $profiles = FanProfile::with(['user', 'favoriteClub'])
            ->orderByDesc('loyalty_points')
            ->limit($request->limit ?? 100)
            ->get();

        return response()->json($profiles);
    }
}
