<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Embedding extends Model
{
    public const TYPE_PELAMAR_CV = 'pelamar_cv';
    public const TYPE_PELAMAR_SKILL = 'pelamar_skill';
    public const TYPE_PELAMAR_EDUCATION = 'pelamar_education';
    public const TYPE_PELAMAR_PENGALAMAN = 'pelamar_pengalaman';
    public const TYPE_LOWONGAN_REQUIREMENT = 'lowongan_requirement';
    public const TYPE_LOWONGAN_TITLE = 'lowongan_title';
    public const TYPE_LOWONGAN_SKILL = 'lowongan_skill';
    public const TYPE_LOWONGAN_EDUCATION = 'lowongan_education';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DONE = 'done';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'embeddable_type',
        'embeddable_id',
        'vector',
        'source_hash',
        'model_version',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'vector' => 'array',
        ];
    }
}