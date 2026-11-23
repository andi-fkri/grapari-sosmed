<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyMetric extends Model
{
    protected $fillable = [
        'metric_date',
        'label',
        'likes',
        'comments',
        'direct_chats',
    ];

    protected $casts = [
        'metric_date' => 'date',
    ];

    protected $appends = [
        'date_key',
    ];

    public function getDateKeyAttribute(): string
    {
        return $this->metric_date ? $this->metric_date->format('Y-m-d') : '';
    }
}
