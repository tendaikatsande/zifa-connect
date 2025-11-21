<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubDocument;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClubController extends Controller
{
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    /**
     * Check if user can modify this club (membership or admin role)
     */
    private function canModifyClub(Request $request, Club $club): bool
    {
        $user = $request->user();

        // Super admins and ZIFA admins can modify any club
        if ($user->hasRole('super_admin') || $user->hasRole('zifa_admin')) {
            return true;
        }

        // User created this club
        if ($club->created_by === $user->id) {
            return true;
        }

        // User is an active official of this club
        $isClubOfficial = $user->clubs()
            ->where('clubs.id', $club->id)
            ->wherePivot('status', 'active')
            ->exists();

        return $isClubOfficial;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Club::with(['region'])
            ->when($request->search, fn($q, $search) =>
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('registration_number', 'ilike', "%{$search}%")
            )
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->region_id, fn($q, $regionId) => $q->where('region_id', $regionId))
            ->when($request->category, fn($q, $cat) => $q->where('category', $cat))
            ->orderBy($request->sort ?? 'name', $request->order ?? 'asc');

        $clubs = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json($clubs);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'region_id' => 'required|exists:regions,id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'home_ground' => 'nullable|string|max:255',
            'established_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'category' => 'nullable|in:premier,division_one,division_two,women,futsal,youth',
        ]);

        $validated['created_by'] = $request->user()->id;
        $validated['status'] = 'pending';

        $club = Club::create($validated);

        return response()->json($club, 201);
    }

    public function show(Club $club): JsonResponse
    {
        $club->load([
            'region',
            'officials.roles',
            'documents',
            'affiliations' => fn($q) => $q->latest(),
            'players' => fn($q) => $q->where('status', 'approved')->limit(50),
        ]);

        $club->loadCount(['players as active_players_count' => fn($q) => $q->where('status', 'approved')]);

        return response()->json($club);
    }

    public function update(Request $request, Club $club): JsonResponse
    {
        // Check membership before allowing update
        if (!$this->canModifyClub($request, $club)) {
            return response()->json(['message' => 'Unauthorized to modify this club'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'home_ground' => 'nullable|string|max:255',
        ]);

        $club->update($validated);

        return response()->json($club);
    }

    public function uploadDocument(Request $request, Club $club): JsonResponse
    {
        // Check membership before allowing document upload
        if (!$this->canModifyClub($request, $club)) {
            return response()->json(['message' => 'Unauthorized to upload documents for this club'], 403);
        }

        $request->validate([
            'type' => 'required|string|in:constitution,registration_certificate,proof_of_payment,logo,other',
            'file' => 'required|file|max:10240',
        ]);

        $path = $request->file('file')->store("clubs/{$club->id}/documents", 'public');

        $document = $club->documents()->create([
            'type' => $request->type,
            'file_url' => Storage::url($path),
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_size' => $request->file('file')->getSize(),
        ]);

        return response()->json($document, 201);
    }

    public function addOfficial(Request $request, Club $club): JsonResponse
    {
        // Check membership before allowing official management
        if (!$this->canModifyClub($request, $club)) {
            return response()->json(['message' => 'Unauthorized to manage officials for this club'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'position' => 'required|string|in:chairman,secretary,treasurer,admin,coach,medic',
        ]);

        $club->officials()->attach($request->user_id, [
            'position' => $request->position,
            'status' => 'active',
            'start_date' => now(),
        ]);

        return response()->json(['message' => 'Official added']);
    }

    public function players(Club $club): JsonResponse
    {
        $players = $club->players()
            ->where('status', 'approved')
            ->with(['activeContract'])
            ->orderBy('last_name')
            ->get();

        return response()->json($players);
    }

    public function renew(Request $request, Club $club): JsonResponse
    {
        // Check membership before allowing renewal
        if (!$this->canModifyClub($request, $club)) {
            return response()->json(['message' => 'Unauthorized to renew this club'], 403);
        }

        return DB::transaction(function () use ($club, $request) {
            $affiliation = $this->registrationService->createAffiliation($club);
            $invoice = $this->registrationService->createAffiliationInvoice($affiliation);

            return response()->json([
                'affiliation' => $affiliation,
                'invoice' => $invoice,
            ]);
        });
    }
}
