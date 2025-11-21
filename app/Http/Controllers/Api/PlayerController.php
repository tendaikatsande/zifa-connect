<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerDocument;
use App\Models\Registration;
use App\Models\Invoice;
use App\Services\RegistrationService;
use App\Traits\LogsAuthorizationDenials;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    use LogsAuthorizationDenials;
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    /**
     * Check if user can modify this player (ownership or admin role)
     */
    private function canModifyPlayer(Request $request, Player $player): bool
    {
        $user = $request->user();

        // Super admins and ZIFA admins can modify any player
        if ($user->hasRole('super_admin') || $user->hasRole('zifa_admin')) {
            return true;
        }

        // User created this player
        if ($player->created_by === $user->id) {
            return true;
        }

        // User is an official of the player's current club
        if ($player->current_club_id) {
            $isClubOfficial = $user->clubs()
                ->where('clubs.id', $player->current_club_id)
                ->wherePivot('status', 'active')
                ->exists();
            if ($isClubOfficial) {
                return true;
            }
        }

        return false;
    }

    public function index(Request $request): JsonResponse
    {
        // Validate input to prevent injection
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:draft,submitted,under_review,approved,rejected',
            'club_id' => 'nullable|integer|exists:clubs,id',
            'category' => 'nullable|string|max:100',
            'sort' => 'nullable|string|in:created_at,first_name,last_name,zifa_id',
            'order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Player::with(['currentClub', 'creator'])
            ->when($validated['search'] ?? null, function ($q, $search) {
                // Use parameterized queries to prevent SQL injection
                $searchTerm = '%' . $search . '%';
                return $q->where(function ($query) use ($searchTerm) {
                    $query->where('first_name', 'ilike', $searchTerm)
                          ->orWhere('last_name', 'ilike', $searchTerm)
                          ->orWhere('zifa_id', 'ilike', $searchTerm);
                });
            })
            ->when($validated['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($validated['club_id'] ?? null, fn($q, $clubId) => $q->where('current_club_id', $clubId))
            ->when($validated['category'] ?? null, fn($q, $cat) => $q->where('registration_category', $cat))
            ->orderBy($validated['sort'] ?? 'created_at', $validated['order'] ?? 'desc');

        // Enforce pagination limits to prevent DoS
        $perPage = min($validated['per_page'] ?? 25, 100);
        $players = $query->paginate($perPage);

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
        // Check ownership/membership before allowing update
        if (!$this->canModifyPlayer($request, $player)) {
            $this->logResourceDenial($request, 'player', $player->id, 'update');
            return response()->json(['message' => 'Unauthorized to modify this player'], 403);
        }

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

    public function destroy(Request $request, Player $player): JsonResponse
    {
        // Check ownership/membership before allowing delete
        if (!$this->canModifyPlayer($request, $player)) {
            $this->logResourceDenial($request, 'player', $player->id, 'delete');
            return response()->json(['message' => 'Unauthorized to delete this player'], 403);
        }

        if ($player->status !== 'draft') {
            return response()->json(['message' => 'Can only delete draft players'], 422);
        }

        $player->delete();

        return response()->json(['message' => 'Player deleted']);
    }

    public function uploadDocument(Request $request, Player $player): JsonResponse
    {
        // Check ownership/membership before allowing document upload
        if (!$this->canModifyPlayer($request, $player)) {
            $this->logResourceDenial($request, 'player', $player->id, 'upload_document');
            return response()->json(['message' => 'Unauthorized to upload documents for this player'], 403);
        }

        $request->validate([
            'type' => 'required|string|in:birth_certificate,national_id,passport,photo,medical,contract',
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB max
                'mimes:pdf,jpg,jpeg,png,doc,docx', // Allowed file types
                'mimetypes:application/pdf,image/jpeg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
        ]);

        $file = $request->file('file');

        // Additional security: check file extension matches content
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        $allowedMimes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        if (!isset($allowedMimes[$extension]) || $allowedMimes[$extension] !== $mimeType) {
            return response()->json([
                'message' => 'File extension does not match content type',
            ], 422);
        }

        // Generate secure filename to prevent path traversal
        $secureFilename = sprintf(
            '%s_%s.%s',
            $request->type,
            now()->format('YmdHis'),
            $extension
        );

        $disk = config('filesystems.documents_disk', 'public');
        $path = $file->storeAs(
            "players/{$player->id}/documents",
            $secureFilename,
            $disk
        );

        $document = $player->documents()->create([
            'type' => $request->type,
            'file_url' => Storage::disk($disk)->url($path),
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json($document, 201);
    }

    public function submit(Request $request, Player $player): JsonResponse
    {
        // Check ownership/membership before allowing submission
        if (!$this->canModifyPlayer($request, $player)) {
            $this->logResourceDenial($request, 'player', $player->id, 'submit');
            return response()->json(['message' => 'Unauthorized to submit this player'], 403);
        }

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
