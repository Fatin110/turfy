<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use App\Enums\Sport;
use App\Enums\TournamentFormat;
use App\Enums\TournamentStatus;
use Database\Factories\TournamentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'slug', 'description', 'rules', 'sport', 'format', 'venue_id', 'created_by',
    'banner_image', 'city', 'start_date', 'end_date', 'registration_deadline',
    'max_teams', 'entry_fee', 'prize_info', 'status',
])]
class Tournament extends Model
{
    /** @use HasFactory<TournamentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'sport' => Sport::class,
            'format' => TournamentFormat::class,
            'status' => TournamentStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'registration_deadline' => 'datetime',
            'entry_fee' => 'decimal:2',
        ];
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function confirmedRegistrations(): HasMany
    {
        return $this->registrations()->where('status', RegistrationStatus::Confirmed);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchGame::class);
    }

    public function isFree(): bool
    {
        return (float) $this->entry_fee === 0.0;
    }

    public function isFull(): bool
    {
        return $this->confirmedRegistrations()->count() >= $this->max_teams;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
