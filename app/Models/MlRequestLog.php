<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MlRequestLog extends Model
{
    protected $table = 'ml_request_logs';

    protected $fillable = [
        'request_id',
        'request_type',
        'endpoint',
        'status',
        'status_code',
        'duration_ms',
        'pelamar_id',
        'lowongan_id',
        'is_update',
        'total_skills',
        'total_pendidikans',
        'total_pengalamans',
        'total_jurusans',
        'total_records_embedded',
        'total_lowongans_sent',
        'total_recommendations',
        'total_pelamars_sent',
        'total_ranked',
        'user_items_count',
        'result_count',
        'model_version',
        'embedding_dimension',
        'error',
        'extra',
    ];

    protected $casts = [
        'is_update' => 'boolean',
        'extra' => 'array',
        'duration_ms' => 'float',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    // ── Scopes ──

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('request_type', $type);
    }

    public function scopeOnlyErrors(Builder $query): Builder
    {
        return $query->where('status', 'error');
    }

    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->where('created_at', '>=', $from);
        }
        if ($to) {
            $query->where('created_at', '<=', $to . ' 23:59:59');
        }
        return $query;
    }

    // ── Helpers ──

    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration_ms === null) {
            return '-';
        }
        return $this->duration_ms >= 1000
            ? number_format($this->duration_ms / 1000, 2) . ' dtk'
            : number_format($this->duration_ms, 2) . ' mntk';
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->request_type) {
            'embedding_pelamar' => 'Embedding Pelamar',
            'embedding_lowongan' => 'Embedding Lowongan',
            'match' => 'CBF Match',
            'rank_applicants' => 'Rank Pelamar',
            'cf_recommendation' => 'CF Rekomendasi',
            default => $this->request_type,
        };
    }

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->request_type) {
            'embedding_pelamar' => 'primary',
            'embedding_lowongan' => 'info',
            'match' => 'success',
            'rank_applicants' => 'warning',
            'cf_recommendation' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Summary stats for the dashboard.
     */
    public static function summaryStats(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $base = fn (Builder $q) => $q->dateRange($dateFrom, $dateTo);

        $total = (clone $base(self::query()))->count();
        $errors = (clone $base(self::query()))->where('status', 'error')->count();

        $avgDuration = (clone $base(self::query()))
            ->where('status', 'success')
            ->avg('duration_ms');

        $byType = (clone $base(self::query()))
            ->selectRaw('request_type, COUNT(*) as total, AVG(duration_ms) as avg_ms, SUM(status = "error") as errors')
            ->groupBy('request_type')
            ->get()
            ->keyBy('request_type');

        return [
            'total' => $total,
            'errors' => $errors,
            'success_rate' => $total > 0 ? round(($total - $errors) / $total * 100, 1) : 0,
            'avg_duration_ms' => round($avgDuration ?? 0, 2),
            'by_type' => $byType,
        ];
    }
}