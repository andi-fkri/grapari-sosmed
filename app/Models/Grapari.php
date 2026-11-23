<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grapari extends Model
{
    protected $fillable = [
        'rank',
        'name',
        'short_name',
        'region',
        'address',
        'total_contents',
        'likes',
        'comments',
        'direct_chats',
        'chart_likes',
        'chart_comments',
        'chart_direct_chats',
        'engagement_rate',
    ];

    public function contents(): HasMany
    {
        return $this->hasMany(SocialContent::class);
    }
}
