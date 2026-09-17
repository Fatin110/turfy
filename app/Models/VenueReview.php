<?php

namespace App\Models;

use Database\Factories\VenueReviewFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['venue_id', 'user_id', 'rating', 'comment'])]
class VenueReview extends Model
{
    /** @use HasFactory<VenueReviewFactory> */
    use HasFactory;

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        $recalculate = function (self $review): void {
            $venue = $review->venue;
            $venue->update([
                'rating_avg' => $venue->reviews()->avg('rating') ?? 0,
                'rating_count' => $venue->reviews()->count(),
            ]);
        };

        static::saved($recalculate);
        static::deleted($recalculate);
    }
}
