<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialContent extends Model
{
    protected $fillable = [
        'grapari_id',
        'post_url',
        'title',
        'campaign_tag',
        'banner_subtitle',
        'platform',
        'likes',
        'comments',
        'direct_chats',
        'status',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = [
        'published_date',
        'published_display',
    ];

    public function grapari(): BelongsTo
    {
        return $this->belongsTo(Grapari::class);
    }

    public function getPublishedDateAttribute(): string
    {
        return $this->published_at ? $this->published_at->format('Y-m-d') : now()->format('Y-m-d');
    }

    public function getPublishedDisplayAttribute(): string
    {
        return $this->published_at ? $this->published_at->format('d M Y • H:i') : now()->format('d M Y • H:i');
    }
}
