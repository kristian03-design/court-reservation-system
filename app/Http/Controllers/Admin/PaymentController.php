<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('admin.payments.index', [
            'payments' => Payment::with(['reservation.court', 'reservation.user'])->latest()->paginate(15),
        ]);
    }

    public function update(Payment $payment, PaymentService $paymentService): RedirectResponse
    {
        $paymentService->markPaid($payment);

        $payment->reservation->user->systemNotifications()->create([
            'title' => 'Payment confirmed',
            'message' => "Payment for {$payment->reservation->reservation_number} has been confirmed.",
        ]);

        return back()->with('success', 'Payment verified.');
    }
}
