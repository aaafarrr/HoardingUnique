<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'uuid',
    'user_id',
    'title',
    'description',
    'image_path',
    'dominant_color',
    'tier',
    'points',
    'image_hash',
    'latitude',
    'longitude',
    'location_name'
])]
class UniqueItem extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::creating(function ($item) {
            $item->uuid = (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'points' => 'integer',
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reposts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Repost::class);
    }
}
