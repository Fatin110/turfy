<?php

namespace App\Services\Payments;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Payment;
use App\Models\TournamentRegistration;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private readonly PaymentGatewayContract $gateway) {}

    /**
     * Instantly confirm a registration for a free tournament, no gateway involved.
     */
    public function completeFreeRegistration(TournamentRegistration $registration): Payment
    {
        return DB::transaction(function () use ($registration) {
            $payment = Payment::create([
                'user_id' => $registration->registered_by,
                'tournament_registration_id' => $registration->id,
                'amount' => 0,
                'method' => PaymentMethod::Free,
                'status' => PaymentStatus::Completed,
                'paid_at' => now(),
            ]);

            $registration->update(['status' => RegistrationStatus::Confirmed]);

            return $payment;
        });
    }

    /**
     * Start a bKash checkout for a paid tournament registration.
     */
    public function initiateBkashPayment(TournamentRegistration $registration, float $amount): Payment
    {
        $payment = Payment::create([
            'user_id' => $registration->registered_by,
            'tournament_registration_id' => $registration->id,
            'amount' => $amount,
            'method' => PaymentMethod::Bkash,
            'status' => PaymentStatus::Pending,
        ]);

        $result = $this->gateway->createPayment($payment);

        $payment->update([
            'bkash_payment_id' => $result['gateway_payment_id'],
            'gateway_response' => $result['raw'],
        ]);

        return $payment;
    }

    /**
     * Confirm (execute) a pending bKash payment and mark the registration confirmed.
     */
    public function confirmBkashPayment(Payment $payment): Payment
    {
        if ($payment->status !== PaymentStatus::Pending || ! $payment->bkash_payment_id) {
            return $payment;
        }

        $result = $this->gateway->executePayment($payment->bkash_payment_id);

        return DB::transaction(function () use ($payment, $result) {
            $payment->update([
                'status' => $result['success'] ? PaymentStatus::Completed : PaymentStatus::Failed,
                'transaction_id' => $result['transaction_id'],
                'gateway_response' => $result['raw'],
                'paid_at' => $result['success'] ? now() : null,
            ]);

            if ($result['success'] && $payment->registration) {
                $payment->registration->update(['status' => RegistrationStatus::Confirmed]);
            }

            return $payment->fresh();
        });
    }
}
