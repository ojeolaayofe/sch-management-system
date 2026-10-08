<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentType extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'max_score', 'weight', 'is_active', 'display_order',
    ];

    protected $casts = [
        'max_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
