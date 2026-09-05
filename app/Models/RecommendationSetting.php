<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationSetting extends Model
{
    protected $fillable = [
        'weight_semantic',
        'weight_skill',
        'weight_education',
        'weight_experience',
        'skill_threshold',
    ];

    protected $casts = [
        'weight_semantic' => 'float',
        'weight_skill' => 'float',
        'weight_education' => 'float',
        'weight_experience' => 'float',
        'skill_threshold' => 'float',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'weight_semantic' => 0.25,
            'weight_skill' => 0.25,
            'weight_education' => 0.25,
            'weight_experience' => 0.25,
            'skill_threshold' => 0.50,
        ]);
    }

    public function toMlConfig(): array
    {
        return [
            'semantic_weight' => $this->weight_semantic,
            'skill_weight' => $this->weight_skill,
            'education_weight' => $this->weight_education,
            'experience_weight' => $this->weight_experience,
            'skill_threshold' => $this->skill_threshold,
        ];
    }
}
