<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferDocument extends Model
{
    protected $fillable = [
        'transfer_id',
        'type',
        'file_url',
        'file_name',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }
}
