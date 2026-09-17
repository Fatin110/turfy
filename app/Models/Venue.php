<?php

namespace App\Models;

use App\Enums\VenueStatus;
use Database\Factories\VenueFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'city', 'address', 'sports', 'cover_image', 'phone', 'status'])]
class Venue extends Model
{
    /** @use HasFactory<VenueFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sports' => 'array',
            'status' => VenueStatus::class,
            'rating_avg' => 'decimal:2',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function team(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(MatchGame::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(VenueReview::class);
    }

    public function isApproved(): bool
    {
        return $this->status === VenueStatus::Approved;
    }

    public function scopeApproved($query)
    {
        return $query->where('status', VenueStatus::Approved);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
