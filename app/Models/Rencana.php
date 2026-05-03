<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rencana extends Model
{
    protected $table = 'rencana';

    protected $fillable = [
        'user_id', 'nama', 'tipe', 'kategori',
        'target', 'terkumpul', 'deadline', 'warna', 'icon',
    ];

    protected $casts = [
        'deadline'  => 'date',
        'target'    => 'integer',
        'terkumpul' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Persentase progress (0–100) */
    public function getPorsenAttribute(): float
    {
        if ($this->target <= 0) return 0;
        return min(round(($this->terkumpul / $this->target) * 100, 1), 100);
    }

    /** Sisa yang dibutuhkan */
    public function getSisaAttribute(): int
    {
        return max($this->target - $this->terkumpul, 0);
    }

    /** Sudah tercapai? */
    public function getIsSelesaiAttribute(): bool
    {
        return $this->terkumpul >= $this->target;
    }

    /** Sisa hari menuju deadline */
    public function getSisaHariAttribute(): ?int
    {
        if (!$this->deadline) return null;
        return max(now()->startOfDay()->diffInDays($this->deadline->startOfDay(), false), 0);
    }
}
