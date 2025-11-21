<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerMedical extends Model
{
    protected $fillable = [
        'player_id',
        'doctor_name',
        'clinic',
        'medical_result',
        'notes',
        'examination_date',
        'expiry_date',
        'certificate_url',
    ];

    protected $casts = [
        'examination_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function isValid(): bool
    {
        return $this->medical_result === 'fit' && (!$this->expiry_date || $this->expiry_date->isFuture());
    }
}
