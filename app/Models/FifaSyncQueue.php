<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FifaSyncQueue extends Model
{
    protected $table = 'fifa_sync_queue';

    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'payload',
        'attempt_count',
        'last_attempt_at',
        'next_attempt_at',
        'status',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'last_attempt_at' => 'datetime',
        'next_attempt_at' => 'datetime',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(FifaSyncLog::class, 'queue_id');
    }
}
