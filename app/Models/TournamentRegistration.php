<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Database\Factories\TournamentRegistrationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['tournament_id', 'team_id', 'registered_by', 'status'])]
class TournamentRegistration extends Model
{
    /** @use HasFactory<TournamentRegistrationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => RegistrationStatus::class,
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
