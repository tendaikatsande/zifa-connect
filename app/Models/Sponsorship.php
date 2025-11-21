<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsorship extends Model
{
    protected $fillable = [
        'sponsor_name',
        'contact_person',
        'contact_email',
        'contact_phone',
        'contract_url',
        'amount_cents',
        'currency',
        'type',
        'start_date',
        'end_date',
        'status',
        'deliverables',
        'meta',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'deliverables' => 'array',
        'meta' => 'array',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active' && (!$this->end_date || $this->end_date->isFuture());
    }
}
