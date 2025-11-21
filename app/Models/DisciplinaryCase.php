<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisciplinaryCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_number',
        'title',
        'description',
        'entity_type',
        'entity_id',
        'charge_type',
        'status',
        'reported_by',
        'assigned_to',
        'incident_date',
        'match_id',
        'hearing_date',
        'hearing_venue',
        'decision',
        'decision_date',
        'evidence',
        'meta',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'hearing_date' => 'datetime',
        'decision_date' => 'datetime',
        'evidence' => 'array',
        'meta' => 'array',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match::class);
    }

    public function sanctions(): HasMany
    {
        return $this->hasMany(DisciplinarySanction::class, 'case_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DisciplinaryDocument::class, 'case_id');
    }

    public function appeals(): HasMany
    {
        return $this->hasMany(Appeal::class, 'case_id');
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
