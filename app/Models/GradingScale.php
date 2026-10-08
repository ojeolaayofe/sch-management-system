<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class GradingScale extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'min_score', 'max_score', 'grade', 'remark', 'is_active', 'display_order',
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
