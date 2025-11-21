<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FanPoll;
use App\Models\FanPollOption;
use App\Models\FanPollVote;
use App\Models\FanProfile;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FanPollController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FanPoll::with(['options', 'match', 'competition'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->match_id, fn($q, $matchId) => $q->where('match_id', $matchId))
            ->when($request->is_featured, fn($q) => $q->where('is_featured', true))
            ->when($request->active, fn($q) => $q->where('status', 'active')
                ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            )
            ->orderByDesc('created_at');

        $polls = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json($polls);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:player_of_match,goal_of_week,best_player,custom',
            'match_id' => 'nullable|exists:matches,id',
            'competition_id' => 'nullable|exists:competitions,id',
            'status' => 'in:draft,active,closed',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_featured' => 'boolean',
            'options' => 'required|array|min:2',
            'options.*.player_id' => 'nullable|exists:players,id',
            'options.*.club_id' => 'nullable|exists:clubs,id',
            'options.*.custom_option' => 'nullable|string|max:255',
            'options.*.image_url' => 'nullable|url',
        ]);

        $poll = DB::transaction(function () use ($validated, $request) {
            $poll = FanPoll::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'match_id' => $validated['match_id'] ?? null,
                'competition_id' => $validated['competition_id'] ?? null,
                'status' => $validated['status'] ?? 'draft',
                'starts_at' => $validated['starts_at'] ?? null,
                'ends_at' => $validated['ends_at'] ?? null,
                'is_featured' => $validated['is_featured'] ?? false,
                'created_by' => $request->user()->id,
            ]);

            foreach ($validated['options'] as $option) {
                FanPollOption::create([
                    'fan_poll_id' => $poll->id,
                    'player_id' => $option['player_id'] ?? null,
                    'club_id' => $option['club_id'] ?? null,
                    'custom_option' => $option['custom_option'] ?? null,
                    'image_url' => $option['image_url'] ?? null,
                ]);
            }

            return $poll;
        });

        return response()->json($poll->load('options'), 201);
    }

    public function show(FanPoll $fanPoll): JsonResponse
    {
        $fanPoll->load(['options.player', 'options.club', 'match', 'competition']);

        return response()->json($fanPoll);
    }

    public function update(Request $request, FanPoll $fanPoll): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:draft,active,closed,archived',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'is_featured' => 'boolean',
        ]);

        $fanPoll->update($validated);

        return response()->json($fanPoll->load('options'));
    }

    public function vote(Request $request, FanPoll $fanPoll): JsonResponse
    {
        if (!$fanPoll->isActive()) {
            return response()->json(['message' => 'This poll is not currently active'], 422);
        }

        $validated = $request->validate([
            'option_id' => 'required|exists:fan_poll_options,id',
        ]);

        // Verify option belongs to this poll
        $option = FanPollOption::where('id', $validated['option_id'])
            ->where('fan_poll_id', $fanPoll->id)
            ->firstOrFail();

        // Check if user already voted
        $existingVote = FanPollVote::where('fan_poll_id', $fanPoll->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingVote) {
            return response()->json(['message' => 'You have already voted in this poll'], 422);
        }

        $vote = FanPollVote::create([
            'fan_poll_id' => $fanPoll->id,
            'fan_poll_option_id' => $validated['option_id'],
            'user_id' => $request->user()->id,
        ]);

        // Award loyalty points for voting
        $fanProfile = FanProfile::where('user_id', $request->user()->id)->first();
        if ($fanProfile) {
            $fanProfile->addLoyaltyPoints(5);
        }

        return response()->json([
            'message' => 'Vote recorded successfully',
            'vote' => $vote,
            'poll' => $fanPoll->load('options'),
        ]);
    }

    public function results(FanPoll $fanPoll): JsonResponse
    {
        $fanPoll->load(['options.player', 'options.club']);

        $results = [
            'poll' => $fanPoll,
            'total_votes' => $fanPoll->totalVotes(),
            'options' => $fanPoll->options->map(fn($option) => [
                'id' => $option->id,
                'display_name' => $option->display_name,
                'votes_count' => $option->votes_count,
                'percentage' => $option->votePercentage(),
                'player' => $option->player,
                'club' => $option->club,
            ]),
            'winner' => $fanPoll->winner(),
        ];

        return response()->json($results);
    }

    public function destroy(FanPoll $fanPoll): JsonResponse
    {
        $fanPoll->delete();

        return response()->json(['message' => 'Poll deleted successfully']);
    }
}
