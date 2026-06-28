@php
    $toasts = collect();

    foreach ([
        'success' => ['type' => 'success', 'title' => 'Success'],
        'status' => ['type' => 'success', 'title' => 'Update'],
        'error' => ['type' => 'error', 'title' => 'Something went wrong'],
        'warning' => ['type' => 'warning', 'title' => 'Heads up'],
        'message' => ['type' => 'info', 'title' => 'Notice'],
    ] as $key => $meta) {
        if (session()->has($key)) {
            $toasts->push($meta + ['messages' => array_values((array) session($key))]);
        }
    }

    if ($errors->any()) {
        $messages = $errors->all();
        $isLoginError = false;
        foreach ($messages as $msg) {
            $lower = strtolower($msg);
            if (
                str_contains($lower, 'credentials') || 
                str_contains($lower, 'incorrect') || 
                str_contains($lower, 'admin accounts must') || 
                str_contains($lower, 'not allowed to access') ||
                str_contains($lower, 'otp')
            ) {
                $isLoginError = true;
                break;
            }
        }
        $toasts->push([
            'type' => 'error',
            'title' => $isLoginError ? 'Login Failed' : 'Please check the highlighted fields.',
            'messages' => $messages,
        ]);
    }
@endphp

@if ($toasts->isNotEmpty())
    <div class="toast-stack" data-toast-stack aria-live="polite" aria-atomic="true">
        @foreach ($toasts as $toast)
            <section class="toast toast-{{ $toast['type'] }}" data-toast role="{{ $toast['type'] === 'error' ? 'alert' : 'status' }}">
                <div class="toast-icon" aria-hidden="true">
                    @if ($toast['type'] === 'success')
                        <i data-lucide="check-circle-2"></i>
                    @elseif ($toast['type'] === 'warning')
                        <i data-lucide="alert-triangle"></i>
                    @elseif ($toast['type'] === 'error')
                        <i data-lucide="alert-triangle"></i>
                    @else
                        <i data-lucide="info"></i>
                    @endif
                </div>
                <div class="toast-content">
                    <strong>{{ $toast['title'] }}</strong>
                    @if (count($toast['messages']) === 1)
                        <p>{{ $toast['messages'][0] }}</p>
                    @else
                        <ul>
                            @foreach ($toast['messages'] as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <button class="toast-close" type="button" data-toast-close aria-label="Dismiss notification">
                    <i data-lucide="x" aria-hidden="true"></i>
                </button>
            </section>
        @endforeach
    </div>

    <script>
        (() => {
            if (typeof window.renderLucideIcons === 'function') {
                window.renderLucideIcons();
            }

            const closeToast = (toast) => {
                toast.classList.add('is-hiding');
                window.setTimeout(() => toast.remove(), 220);
            };

            document.querySelectorAll('[data-toast]').forEach((toast) => {
                toast.querySelector('[data-toast-close]')?.addEventListener('click', () => closeToast(toast));
                window.setTimeout(() => closeToast(toast), toast.classList.contains('toast-error') ? 8000 : 5200);
            });
        })();
    </script>
@endif
