<?php

namespace App\Models;

use Database\Factories\IncidentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    /** @use HasFactory<IncidentFactory> */
    use HasFactory;

    protected $table = 'incidents';

    protected $fillable = [
        'ticket_number',
        'user_id',
        'assigned_to',
        'category_id',
        'title',
        'description',
        'priority',
        'status',
        'occurred_at',
        'related_url',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'resolved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Normalize status values between legacy seeded data and standard enum keys.
     */
    public function getNormalizedStatusAttribute(): string
    {
        $raw = strtolower(trim((string) $this->status));

        return match ($raw) {
            'pending', 'open', 'menunggu' => 'pending',
            'investigating', 'investigasi' => 'investigating',
            'in_progress', 'diproses', 'dalam penanganan' => 'in_progress',
            'resolved', 'selesai' => 'resolved',
            'rejected', 'ditolak', 'closed' => 'rejected',
            default => 'pending',
        };
    }

    /**
     * Clear Indonesian label for status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->normalized_status) {
            'pending' => 'Menunggu Tinjauan (Open)',
            'investigating' => 'Dalam Investigasi (In Progress)',
            'in_progress' => 'Sedang Ditangani (Diproses)',
            'resolved' => 'Selesai Ditangani (Resolved)',
            'rejected' => 'Ditolak / Ditutup (Closed)',
            default => 'Menunggu Tinjauan (Open)',
        };
    }

    /**
     * Short Indonesian badge text for status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->normalized_status) {
            'pending' => 'Menunggu',
            'investigating' => 'Investigasi',
            'in_progress' => 'Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak',
            default => 'Menunggu',
        };
    }

    /**
     * Normalize priority values.
     */
    public function getNormalizedPriorityAttribute(): string
    {
        $raw = strtolower(trim((string) $this->priority));

        return match ($raw) {
            'critical', 'kritis' => 'critical',
            'high', 'tinggi' => 'high',
            'medium', 'sedang' => 'medium',
            default => 'low',
        };
    }

    /**
     * Clear Indonesian label for priority.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->normalized_priority) {
            'critical' => 'Kritis (Critical)',
            'high' => 'Tinggi (High)',
            'medium' => 'Sedang (Medium)',
            default => 'Rendah (Low)',
        };
    }

    /**
     * Short Indonesian badge text for priority.
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->normalized_priority) {
            'critical' => 'Kritis',
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            default => 'Rendah',
        };
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(IncidentCategory::class, 'category_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(IncidentAttachment::class, 'incident_id');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(IncidentUpdate::class, 'incident_id');
    }
}
