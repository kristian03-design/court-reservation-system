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

        $paymentService->uploadProof($payment, $request->file('proof_image'), $request->validated('reference_number'));

        return back()->with('success', 'Payment proof uploaded for verification.');
    }

    public function payCard(Payment $payment, PaymentService $paymentService): RedirectResponse
    {
        $this->authorize('view', $payment->reservation);

        request()->validate([
            'card_number' => ['required', 'string', 'min:15', 'max:19'],
            'card_expiry' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/?([0-9]{2})$/'],
            'card_cvc' => ['required', 'string', 'digits_between:3,4'],
        ]);

        $paymentService->payWithCard($payment);

        return back()->with('success', 'Credit card payment processed and booking confirmed!');
    }

    public function showProof(Payment $payment): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('view', $payment->reservation);

        if (!$payment->proof_image) {
            abort(404);
        }

        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($payment->proof_image);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
