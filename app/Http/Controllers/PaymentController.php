<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\Payments\PaymentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function checkout(Payment $payment): Response
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        if ($payment->status !== PaymentStatus::Pending) {
            return $this->redirectAfterPayment($payment);
        }

        return Inertia::render('Payments/Checkout', [
            'payment' => $payment->load('registration.tournament:id,name,slug', 'registration.team:id,name'),
        ]);
    }

    public function pay(Payment $payment): RedirectResponse
    {
        abort_unless($payment->user_id === auth()->id(), 403);

        $payment = $this->payments->confirmBkashPayment($payment);

        return $this->redirectAfterPayment($payment);
    }

    private function redirectAfterPayment(Payment $payment): RedirectResponse
    {
        if ($payment->status === PaymentStatus::Completed) {
            return to_route('payments.success', $payment);
        }

        return to_route('tournaments.show', $payment->registration->tournament)
            ->with('error', 'Payment failed. Please try again.');
    }

    public function success(Payment $payment): Response
    {
        abort_unless($payment->user_id === auth()->id(), 403);
        abort_unless($payment->status === PaymentStatus::Completed, 404);

        return Inertia::render('Payments/Success', [
            'payment' => $payment->load('registration.tournament:id,name,slug', 'registration.team:id,name'),
        ]);
    }
}
