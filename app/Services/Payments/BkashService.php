<?php

namespace App\Services\Payments;

use App\Models\Payment;
use Illuminate\Support\Str;

/**
 * Stub for bKash's Tokenized Checkout Payment Gateway.
 *
 * Mirrors the real create/execute lifecycle (see bKash's PGW API docs) so
 * that swapping in real HTTP calls to https://tokenized.bka.sh only requires
 * filling in the two methods below with actual Guzzle requests using the
 * credentials in config('bkash'), without touching any calling code.
 */
class BkashService implements PaymentGatewayContract
{
    public function createPayment(Payment $payment): array
    {
        $gatewayPaymentId = 'TR' . Str::upper(Str::random(10));

        return [
            'gateway_payment_id' => $gatewayPaymentId,
            'redirect_url' => null, // a real integration returns bKash's hosted checkout URL
            'raw' => [
                'statusCode' => '0000',
                'statusMessage' => 'Successful',
                'paymentID' => $gatewayPaymentId,
                'amount' => (string) $payment->amount,
                'currency' => $payment->currency,
                'intent' => 'sale',
            ],
        ];
    }

    public function executePayment(string $gatewayPaymentId): array
    {
        $transactionId = 'BKS' . Str::upper(Str::random(8));

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'raw' => [
                'statusCode' => '0000',
                'statusMessage' => 'Successful',
                'paymentID' => $gatewayPaymentId,
                'trxID' => $transactionId,
                'transactionStatus' => 'Completed',
            ],
        ];
    }
}
