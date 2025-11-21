<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FanNewsComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fan_news_id',
        'user_id',
        'parent_id',
        'content',
        'status',
        'likes_count',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(FanNews::class, 'fan_news_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FanNewsComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(FanNewsComment::class, 'parent_id');
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    public function flag(): void
    {
        $this->update(['status' => 'flagged']);
    }

    public function like(): void
    {
        $this->increment('likes_count');
    }
}
