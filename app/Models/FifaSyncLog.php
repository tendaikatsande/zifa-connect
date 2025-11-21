<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FifaSyncLog extends Model
{
    protected $fillable = [
        'queue_id',
        'entity_type',
        'entity_id',
        'action',
        'request_payload',
        'response_payload',
        'response_code',
        'status',
        'error_message',
        'synced_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'synced_at' => 'datetime',
    ];

    public function queue(): BelongsTo
    {
        return $this->belongsTo(FifaSyncQueue::class, 'queue_id');
    }
}
