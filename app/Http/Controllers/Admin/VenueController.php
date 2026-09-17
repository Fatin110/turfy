<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VenueStatus;
use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VenueController extends Controller
{
    public function index(Request $request): Response
    {
        $venues = Venue::query()
            ->when($request->string('status')->toString(), fn ($q, $status) => $q->where('status', $status))
            ->with('owner:id,name,email')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Venues/Index', [
            'venues' => $venues,
            'filters' => $request->only('status'),
        ]);
    }

    public function approve(Venue $venue): RedirectResponse
    {
        $this->authorize('approve', $venue);

        $venue->update(['status' => VenueStatus::Approved]);

        return back()->with('success', "{$venue->name} approved.");
    }

    public function reject(Venue $venue): RedirectResponse
    {
        $this->authorize('approve', $venue);

        $venue->update(['status' => VenueStatus::Rejected]);

        return back()->with('success', "{$venue->name} rejected.");
    }
}
