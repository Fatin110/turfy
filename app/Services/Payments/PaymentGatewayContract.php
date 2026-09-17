<?php

namespace App\Services\Payments;

use App\Models\Payment;

interface PaymentGatewayContract
{
    /**
     * Create a payment on the gateway's side and return the details needed
     * to redirect the payer / render a checkout screen.
     *
     * @return array{gateway_payment_id: string, redirect_url: ?string, raw: array}
     */
    public function createPayment(Payment $payment): array;

    /**
     * Execute (capture) a previously created gateway payment.
     *
     * @return array{success: bool, transaction_id: ?string, raw: array}
     */
    public function executePayment(string $gatewayPaymentId): array;
}
