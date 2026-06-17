<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\UploadedFile;

class PaymentService
{
    public function uploadProof(Payment $payment, UploadedFile $file): Payment
    {
        $path = $file->store('payment-proofs', 'public');

        $payment->update([
            'proof_image' => $path,
            'payment_status' => 'pending_verification',
        ]);

        return $payment;
    }

    public function markPaid(Payment $payment): Payment
    {
        $payment->update([
            'payment_status' => 'paid',
            'verified_at' => now(),
        ]);

        return $payment;
    }
}
