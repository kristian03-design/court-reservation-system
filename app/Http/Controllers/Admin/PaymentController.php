<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('admin.payments.admin-payments', [
            'payments' => Payment::with(['reservation.court', 'reservation.user'])->latest()->paginate(15),
        ]);
    }
    public function show(Payment $payment): View
    {
        return view('admin.payments.admin-payment-show', [
            'payment' => $payment->load(['reservation.court', 'reservation.user']),
        ]);
    }

    public function update(Request $request, Payment $payment, PaymentService $paymentService): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'rejection_reason' => ['required_if:action,reject', 'nullable', 'string', 'max:500'],
        ]);

        if ($data['action'] === 'approve') {
            $paymentService->markPaid($payment);

            \App\Services\AuditLogService::log('payment_status_updated', $payment, [
                'reservation_number' => $payment->reservation->reservation_number,
                'status' => 'paid',
            ]);

            $payment->reservation->user->systemNotifications()->create([
                'title' => 'Payment confirmed',
                'message' => "Payment for {$payment->reservation->reservation_number} has been confirmed.",
            ]);

            return back()->with('success', 'Payment verified and reservation approved.');
        } else {
            $paymentService->markRejected($payment, $data['rejection_reason']);

            \App\Services\AuditLogService::log('payment_status_updated', $payment, [
                'reservation_number' => $payment->reservation->reservation_number,
                'status' => 'rejected',
                'reason' => $data['rejection_reason'],
            ]);

            $payment->reservation->user->systemNotifications()->create([
                'title' => 'Payment Proof Rejected',
                'message' => "Payment proof for {$payment->reservation->reservation_number} was rejected. Reason: {$data['rejection_reason']}.",
            ]);

            return back()->with('success', 'Payment proof rejected.');
        }
    }

    public function showProof(Payment $payment): \Symfony\Component\HttpFoundation\Response
    {
        if (!$payment->proof_image) {
            abort(404);
        }

        $diskName = config('filesystems.default');
        $disk = \Illuminate\Support\Facades\Storage::disk($diskName);

        if ($diskName === 'local') {
            $path = $disk->path($payment->proof_image);
            if (!file_exists($path)) {
                abort(404);
            }
            return response()->file($path);
        }

        if (!$disk->exists($payment->proof_image)) {
            abort(404);
        }

        return response()->stream(function () use ($disk, $payment) {
            $stream = $disk->readStream($payment->proof_image);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $disk->mimeType($payment->proof_image),
            'Content-Disposition' => 'inline; filename="' . basename($payment->proof_image) . '"',
        ]);
    }
}
