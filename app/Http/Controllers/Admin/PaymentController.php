<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $payments = Payment::query()
            ->when($request->string('status')->toString(), fn ($q, $status) => $q->where('status', $status))
            ->with(['user:id,name,email', 'registration.tournament:id,name,slug', 'registration.team:id,name'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $payments,
            'filters' => $request->only('status'),
        ]);
    }
}
