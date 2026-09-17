<?php

namespace App\Models;

use App\Enums\Sport;
use App\Enums\TeamType;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'logo', 'type', 'sport', 'city', 'venue_id', 'captain_id'])]
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => TeamType::class,
            'sport' => Sport::class,
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function captain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_id');
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['jersey_number', 'role'])
            ->withTimestamps();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function leagues(): BelongsToMany
    {
        return $this->belongsToMany(League::class)->withTimestamps();
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(MatchGame::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(MatchGame::class, 'away_team_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Teams a user can act on behalf of: their roster teams, teams they
     * captain, and — for a venue's team — the venue they own.
     */
    public function scopeManageableBy($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('captain_id', $user->id)
                ->orWhereHas('players', fn ($q2) => $q2->where('users.id', $user->id))
                ->orWhereHas('venue', fn ($q2) => $q2->where('owner_id', $user->id));
        });
    }
}
