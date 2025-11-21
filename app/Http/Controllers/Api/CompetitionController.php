<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompetitionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Competition::with(['region'])
            ->when($request->season, fn($q, $season) => $q->where('season', $season))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->orderBy('start_date', 'desc');

        $competitions = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json($competitions);
    }

    public function show(Competition $competition): JsonResponse
    {
        $competition->load(['region', 'teams']);
        return response()->json($competition);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'type' => 'required|in:league,cup,tournament,friendly',
            'season' => 'required|string',
            'region_id' => 'nullable|exists:regions,id',
            'age_group' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_teams' => 'nullable|integer|min:2',
            'entry_fee_usd' => 'nullable|numeric|min:0',
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'planned';

        $competition = Competition::create($validated);

        return response()->json($competition, 201);
    }

    public function standings(Competition $competition): JsonResponse
    {
        $standings = $competition->standings()->get();
        return response()->json($standings);
    }

    public function matches(Competition $competition): JsonResponse
    {
        $matches = $competition->matches()
            ->with(['homeClub', 'awayClub', 'referee.user'])
            ->orderBy('match_date')
            ->get();

        return response()->json($matches);
    }
}
