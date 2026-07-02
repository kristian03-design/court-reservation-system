<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Http\UploadedFile;

class PaymentService
{
    public function uploadProof(Payment $payment, UploadedFile $file, string $referenceNumber): Payment
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($file->getMimeType(), $allowedMimes, true) || !in_array($extension, $allowedExtensions, true)) {
            throw new \InvalidArgumentException('Invalid file type. Only JPG, PNG, and WEBP images are allowed.');
        }

        $uuid = (string) \Illuminate\Support\Str::uuid();
        $filename = "{$uuid}.{$extension}";

        // Store in default filesystem disk
        $diskName = config('filesystems.default');
        $path = $file->storeAs('payment-proofs', $filename, $diskName);

        $payment->update([
            'proof_image' => $path,
            'reference_number' => $referenceNumber,
            'payment_status' => 'pending_verification',
            'rejection_reason' => null, // Reset reason on re-upload
        ]);

        $payment->reservation->update([
            'status' => 'pending_payment',
        ]);

        \App\Services\AuditLogService::log('payment_proof_uploaded', $payment, [
            'reservation_number' => $payment->reservation->reservation_number,
            'amount' => $payment->amount,
        ]);

        \App\Models\AdminNotification::create([
            'type'       => 'payment',
            'title'      => 'Payment Proof Uploaded',
            'message'    => "{$payment->reservation->user->name} uploaded proof for reservation {$payment->reservation->reservation_number} (₱" . number_format((float) $payment->amount, 2) . ").",
            'action_url' => route('admin.payments.show', $payment),
        ]);

        return $payment;
    }

    public function payWithCard(Payment $payment): Payment
    {
        $this->markPaid($payment);

        \App\Services\AuditLogService::log('payment_card_processed', $payment, [
            'reservation_number' => $payment->reservation->reservation_number,
            'amount' => $payment->amount,
        ]);

        \App\Models\AdminNotification::create([
            'type'       => 'payment',
            'title'      => 'Instant Payment Completed',
            'message'    => "{$payment->reservation->user->name} paid via Credit Card for reservation {$payment->reservation->reservation_number} (₱" . number_format((float) $payment->amount, 2) . ").",
            'action_url' => route('admin.payments.show', $payment),
        ]);

        return $payment;
    }

    public function markPaid(Payment $payment): Payment
    {
        $payment->update([
            'payment_status' => 'paid',
            'verified_at' => now(),
        ]);

        $payment->reservation->update([
            'status' => 'confirmed',
        ]);

        return $payment;
    }

    public function markRejected(Payment $payment, string $reason): Payment
    {
        $payment->update([
            'payment_status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        $payment->reservation->update([
            'status' => 'cancelled',
        ]);

        return $payment;
    }
}
