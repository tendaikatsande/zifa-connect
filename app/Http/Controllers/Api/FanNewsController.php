<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FanNews;
use App\Models\FanNewsComment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class FanNewsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FanNews::with(['club', 'creator'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status), fn($q) => $q->published())
            ->when($request->category, fn($q, $category) => $q->where('category', $category))
            ->when($request->club_id, fn($q, $clubId) => $q->where('club_id', $clubId))
            ->when($request->is_featured, fn($q) => $q->featured())
            ->when($request->search, fn($q, $search) =>
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('content', 'ilike', "%{$search}%")
            )
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at');

        $news = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json($news);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|url',
            'category' => 'required|in:announcement,match_preview,match_report,transfer,interview,general',
            'status' => 'in:draft,published',
            'club_id' => 'nullable|exists:clubs,id',
            'match_id' => 'nullable|exists:matches,id',
            'is_featured' => 'boolean',
            'is_pinned' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['created_by'] = $request->user()->id;

        if (($validated['status'] ?? 'draft') === 'published') {
            $validated['published_at'] = now();
        }

        $news = FanNews::create($validated);

        return response()->json($news->load('club'), 201);
    }

    public function show(FanNews $fanNews): JsonResponse
    {
        $fanNews->incrementViews();
        $fanNews->load(['club', 'match', 'creator', 'approvedComments.user']);

        return response()->json($fanNews);
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $news = FanNews::where('slug', $slug)->published()->firstOrFail();
        $news->incrementViews();
        $news->load(['club', 'match', 'creator', 'approvedComments.user']);

        return response()->json($news);
    }

    public function update(Request $request, FanNews $fanNews): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'string',
            'featured_image' => 'nullable|url',
            'category' => 'in:announcement,match_preview,match_report,transfer,interview,general',
            'status' => 'in:draft,published,archived',
            'club_id' => 'nullable|exists:clubs,id',
            'match_id' => 'nullable|exists:matches,id',
            'is_featured' => 'boolean',
            'is_pinned' => 'boolean',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (isset($validated['status']) && $validated['status'] === 'published' && !$fanNews->published_at) {
            $validated['published_at'] = now();
        }

        $fanNews->update($validated);

        return response()->json($fanNews->load('club'));
    }

    public function destroy(FanNews $fanNews): JsonResponse
    {
        $fanNews->delete();

        return response()->json(['message' => 'News article deleted successfully']);
    }

    public function addComment(Request $request, FanNews $fanNews): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:fan_news_comments,id',
        ]);

        $comment = FanNewsComment::create([
            'fan_news_id' => $fanNews->id,
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content'],
            'status' => 'pending',
        ]);

        return response()->json($comment->load('user'), 201);
    }

    public function comments(FanNews $fanNews): JsonResponse
    {
        $comments = $fanNews->approvedComments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($comments);
    }

    public function moderateComment(Request $request, FanNewsComment $comment): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,flag',
        ]);

        match ($validated['action']) {
            'approve' => $comment->approve(),
            'reject' => $comment->reject(),
            'flag' => $comment->flag(),
        };

        return response()->json($comment);
    }

    public function likeComment(FanNewsComment $comment): JsonResponse
    {
        $comment->like();

        return response()->json($comment);
    }

    public function featured(): JsonResponse
    {
        $news = FanNews::published()
            ->featured()
            ->with('club')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return response()->json($news);
    }

    public function latest(): JsonResponse
    {
        $news = FanNews::published()
            ->with('club')
            ->orderByDesc('published_at')
            ->limit(10)
            ->get();

        return response()->json($news);
    }
}
