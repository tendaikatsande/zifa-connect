<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\Player;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function __construct(
        private TransferService $transferService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Transfer::with(['player', 'fromClub', 'toClub'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->club_id, fn($q, $clubId) =>
                $q->where('from_club_id', $clubId)->orWhere('to_club_id', $clubId)
            )
            ->orderBy('created_at', 'desc');

        $transfers = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json($transfers);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'to_club_id' => 'required|exists:clubs,id',
            'type' => 'required|in:local,international,loan,free',
            'transfer_fee_usd' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $player = Player::findOrFail($validated['player_id']);

        // Validation checks
        if (!$player->isEligibleForTransfer()) {
            return response()->json(['message' => 'Player is not eligible for transfer'], 422);
        }

        if ($player->current_club_id === $validated['to_club_id']) {
            return response()->json(['message' => 'Player is already at this club'], 422);
        }

        // Check transfer window
        if (!$this->transferService->isTransferWindowOpen()) {
            return response()->json(['message' => 'Transfer window is closed'], 422);
        }

        $transfer = DB::transaction(function () use ($validated, $player, $request) {
            return $this->transferService->initiateTransfer(
                $player,
                $validated['to_club_id'],
                $validated['type'],
                $validated['transfer_fee_usd'] ?? 0,
                $validated['notes'] ?? null,
                $request->user()
            );
        });

        return response()->json($transfer, 201);
    }

    public function show(Transfer $transfer): JsonResponse
    {
        $transfer->load([
            'player',
            'fromClub',
            'toClub',
            'documents',
            'requester',
        ]);

        return response()->json($transfer);
    }

    public function approveFromClub(Request $request, Transfer $transfer): JsonResponse
    {
        if ($transfer->status !== 'pending_from_club') {
            return response()->json(['message' => 'Transfer is not pending club approval'], 422);
        }

        // Verify user belongs to from_club
        $userClubs = $request->user()->clubs->pluck('id')->toArray();
        if (!in_array($transfer->from_club_id, $userClubs)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->transferService->approveByClub($transfer, $request->user());

        return response()->json(['message' => 'Transfer approved by club']);
    }

    public function approveZifa(Request $request, Transfer $transfer): JsonResponse
    {
        if ($transfer->status !== 'pending_zifa_review') {
            return response()->json(['message' => 'Transfer is not pending ZIFA review'], 422);
        }

        DB::transaction(function () use ($transfer, $request) {
            $this->transferService->approveByZifa($transfer, $request->user());
        });

        return response()->json([
            'message' => 'Transfer approved',
            'certificate_url' => $transfer->fresh()->certificate_url
        ]);
    }

    public function reject(Request $request, Transfer $transfer): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $this->transferService->reject($transfer, $request->reason, $request->user());

        return response()->json(['message' => 'Transfer rejected']);
    }
}
