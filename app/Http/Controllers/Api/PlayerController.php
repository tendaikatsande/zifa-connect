<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerDocument;
use App\Models\Registration;
use App\Models\Invoice;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Player::with(['currentClub', 'creator'])
            ->when($request->search, fn($q, $search) =>
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('zifa_id', 'ilike', "%{$search}%")
            )
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->club_id, fn($q, $clubId) => $q->where('current_club_id', $clubId))
            ->when($request->category, fn($q, $cat) => $q->where('registration_category', $cat))
            ->orderBy($request->sort ?? 'created_at', $request->order ?? 'desc');

        $players = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json($players);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'other_names' => 'nullable|string|max:255',
            'dob' => 'required|date|before:today',
            'gender' => 'required|in:M,F,Other',
            'nationality' => 'required|string|max:100',
            'place_of_birth' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'national_id' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'current_club_id' => 'nullable|exists:clubs,id',
            'registration_category' => 'required|string',
            'primary_position' => 'nullable|string|max:50',
            'secondary_position' => 'nullable|string|max:50',
            'height_cm' => 'nullable|integer|min:100|max:250',
            'weight_kg' => 'nullable|integer|min:30|max:200',
            'dominant_foot' => 'nullable|in:left,right,both',
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'draft';

        $player = Player::create($validated);

        return response()->json($player, 201);
    }

    public function show(Player $player): JsonResponse
    {
        $player->load([
            'currentClub',
            'documents',
            'contracts' => fn($q) => $q->latest(),
            'medicals' => fn($q) => $q->latest(),
            'statistics' => fn($q) => $q->latest(),
            'transferHistory.club',
            'registrations' => fn($q) => $q->latest(),
        ]);

        return response()->json($player);
    }

    public function update(Request $request, Player $player): JsonResponse
    {
        if (!in_array($player->status, ['draft', 'rejected'])) {
            return response()->json(['message' => 'Cannot edit player in current status'], 422);
        }

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'other_names' => 'nullable|string|max:255',
            'dob' => 'sometimes|date|before:today',
            'gender' => 'sometimes|in:M,F,Other',
            'nationality' => 'sometimes|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'primary_position' => 'nullable|string|max:50',
            'height_cm' => 'nullable|integer|min:100|max:250',
            'weight_kg' => 'nullable|integer|min:30|max:200',
        ]);

        $player->update($validated);

        return response()->json($player);
    }

    public function destroy(Player $player): JsonResponse
    {
        if ($player->status !== 'draft') {
            return response()->json(['message' => 'Can only delete draft players'], 422);
        }

        $player->delete();

        return response()->json(['message' => 'Player deleted']);
    }

    public function uploadDocument(Request $request, Player $player): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:birth_certificate,national_id,passport,photo,medical,contract',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $path = $request->file('file')->store("players/{$player->id}/documents", 'public');

        $document = $player->documents()->create([
            'type' => $request->type,
            'file_url' => Storage::url($path),
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_size' => $request->file('file')->getSize(),
        ]);

        return response()->json($document, 201);
    }

    public function submit(Request $request, Player $player): JsonResponse
    {
        if ($player->status !== 'draft') {
            return response()->json(['message' => 'Can only submit draft players'], 422);
        }

        // Validate required documents
        $requiredDocs = config('zifa.registration.player.required_documents');
        $uploadedTypes = $player->documents->pluck('type')->toArray();
        $missing = array_diff($requiredDocs, $uploadedTypes);

        if (!empty($missing)) {
            return response()->json([
                'message' => 'Missing required documents',
                'missing' => $missing
            ], 422);
        }

        DB::transaction(function () use ($player, $request) {
            // Create registration
            $registration = $this->registrationService->createPlayerRegistration($player);

            // Create invoice
            $this->registrationService->createRegistrationInvoice($registration);

            $player->update(['status' => 'submitted']);
        });

        return response()->json(['message' => 'Player submitted for registration']);
    }

    public function approve(Request $request, Player $player): JsonResponse
    {
        if ($player->status !== 'under_review') {
            return response()->json(['message' => 'Player is not under review'], 422);
        }

        DB::transaction(function () use ($player, $request) {
            // Generate ZIFA ID
            $zifaId = $this->registrationService->generateZifaId('player');

            $player->update([
                'status' => 'approved',
                'zifa_id' => $zifaId,
            ]);

            // Update registration
            $registration = $player->registrations()->latest()->first();
            if ($registration) {
                $registration->update([
                    'status' => 'approved',
                    'reviewed_by' => $request->user()->id,
                    'reviewed_at' => now(),
                ]);
            }

            // Queue FIFA Connect sync
            $this->registrationService->queueFifaSync('player', $player->id, 'create');
        });

        return response()->json(['message' => 'Player approved', 'zifa_id' => $player->fresh()->zifa_id]);
    }

    public function reject(Request $request, Player $player): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($player->status !== 'under_review') {
            return response()->json(['message' => 'Player is not under review'], 422);
        }

        DB::transaction(function () use ($player, $request) {
            $player->update(['status' => 'rejected']);

            $registration = $player->registrations()->latest()->first();
            if ($registration) {
                $registration->update([
                    'status' => 'rejected',
                    'reviewed_by' => $request->user()->id,
                    'reviewed_at' => now(),
                    'rejection_reason' => $request->reason,
                ]);
            }
        });

        return response()->json(['message' => 'Player rejected']);
    }
}
