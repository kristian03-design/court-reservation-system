<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function store(StorePaymentRequest $request, Payment $payment, PaymentService $paymentService): RedirectResponse
    {
        $this->authorize('view', $payment->reservation);

        $paymentService->uploadProof($payment, $request->file('proof_image'));

        return back()->with('success', 'Payment proof uploaded for verification.');
    }
}
