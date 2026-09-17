<?php

namespace App\Http\Controllers;

use App\Enums\Sport;
use App\Enums\TeamType;
use App\Enums\VenueStatus;
use App\Models\Team;
use App\Models\Venue;
use App\Support\Slug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VenueController extends Controller
{
    public function index(Request $request): Response
    {
        $venues = Venue::query()
            ->where('status', VenueStatus::Approved)
            ->when($request->string('city')->toString(), fn ($q, $city) => $q->where('city', $city))
            ->when($request->string('sport')->toString(), fn ($q, $sport) => $q->whereJsonContains('sports', $sport))
            ->when($request->string('search')->toString(), fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->orderByDesc('rating_avg')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Venues/Index', [
            'venues' => $venues,
            'cities' => Venue::where('status', VenueStatus::Approved)->distinct()->orderBy('city')->pluck('city'),
            'sports' => collect(Sport::cases())->map->value,
            'filters' => $request->only(['city', 'sport', 'search']),
        ]);
    }

    public function show(Venue $venue): Response
    {
        abort_unless($venue->isApproved() || (auth()->check() && (auth()->user()->isAdmin() || auth()->id() === $venue->owner_id)), 404);

        $venue->load([
            'team',
            'reviews.user:id,name',
            'tournaments' => fn ($q) => $q->orderByDesc('start_date')->limit(10),
        ]);

        return Inertia::render('Venues/Show', [
            'venue' => $venue,
            'canReview' => auth()->check(),
            'userReview' => auth()->check() ? $venue->reviews->firstWhere('user_id', auth()->id()) : null,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Venue::class);

        return Inertia::render('Venues/Form', [
            'sports' => collect(Sport::cases())->map->value,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Venue::class);

        $data = $this->validated($request);

        $venue = $request->user()->venues()->create([
            ...$data,
            'slug' => Slug::unique($data['name'], Venue::class),
            'status' => VenueStatus::Pending,
        ]);

        $venue->team()->create([
            'name' => $venue->name,
            'slug' => Slug::unique($venue->name, Team::class),
            'type' => TeamType::Venue,
            'sport' => $data['sports'][0],
            'city' => $venue->city,
        ]);

        return to_route('venues.show', $venue)->with('success', 'Venue submitted! An admin will review it shortly.');
    }

    public function edit(Venue $venue): Response
    {
        $this->authorize('update', $venue);

        return Inertia::render('Venues/Form', [
            'venue' => $venue,
            'sports' => collect(Sport::cases())->map->value,
        ]);
    }

    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $this->authorize('update', $venue);

        $venue->update($this->validated($request, $venue));

        return to_route('venues.show', $venue)->with('success', 'Venue updated.');
    }

    public function storeReview(Request $request, Venue $venue): RedirectResponse
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $venue->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $data
        );

        return back()->with('success', 'Thanks for your review!');
    }

    private function validated(Request $request, ?Venue $venue = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
            'sports' => ['required', 'array', 'min:1'],
            'sports.*' => ['string', 'in:cricket,football'],
        ]);
    }
}
