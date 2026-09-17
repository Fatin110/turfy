<?php

namespace App\Models;

use App\Enums\LeagueStatus;
use App\Enums\Sport;
use Database\Factories\LeagueFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'slug', 'description', 'sport', 'season', 'city', 'created_by', 'start_date', 'end_date',
    'points_win', 'points_draw', 'points_loss', 'double_round_robin', 'status',
])]
class League extends Model
{
    /** @use HasFactory<LeagueFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sport' => Sport::class,
            'status' => LeagueStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'double_round_robin' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withTimestamps();
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchGame::class)->orderBy('matchday');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
