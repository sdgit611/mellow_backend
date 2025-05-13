@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-gradient bg-primary text-white rounded-top-4">
                    <h4 class="mb-0 text-center">
                        Swap Checkout <br>
                        <span class="fs-6 text-light">{{ $user->full_name ?? 'N/A' }} ➜ {{ $user_swap->full_name ?? 'N/A' }}</span>
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Swap Summary -->
                    <h5 class="mb-4 text-primary">Swap Summary</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Label</th>
                                    <th scope="col">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>From ({{ $user_swap->full_name ?? 'N/A' }})</strong></td>
                                    <td>₹{{ number_format($user_swap->current_ctc ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>To ({{ $user->full_name ?? 'N/A' }})</strong></td>
                                    <td>₹{{ number_format($user->current_ctc ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Paid Already</strong></td>
                                    <td>₹{{ number_format($user->payment_amount ?? 0, 2) }}</td>
                                </tr>
                                <tr class="table-warning">
                                    <td><strong>Remaining Amount</strong></td>
                                    <td>
                                        ₹{{ number_format(($user_swap->current_ctc ?? 0) - ($user->payment_amount ?? 0), 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pay Now Button -->
                    <div class="d-flex justify-content-end">
                        <a 
                           class="btn btn-lg btn-success px-5"
                           id="paymentButton" 
                        >
                            Pay Now ₹{{ number_format(($user_swap->current_ctc ?? 0) - ($user->payment_amount ?? 0), 2) }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('paymentButton').addEventListener('click', function () {
    // Unformatted raw amount from backend (must be passed without commas)
    const rawAmount = {{ ($user_swap->current_ctc ?? 0) - ($user->payment_amount ?? 0) }};
    const amountInPaise = Math.round(rawAmount * 100); // Razorpay accepts paise

    const options = {
        "key": "{{ env('RAZORPAY_KEY') }}", // Razorpay Key from .env
        "amount": amountInPaise,
        "currency": "INR",
        "name": "Swap Payment",
        "description": "Checkout for swap between users",
        "handler": function (response) {
            // Optional: Send payment_id to your backend to verify and complete order

            fetch("{{ route('swap.save') }}", {
                method: 'POST',
                body: JSON.stringify({
                    id: {{ $id }},
                    amount : rawAmount
                }),
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
            })
            .then(response => response.json())
            .then(data => {
                alert("Payment");
                
                if (data.success && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    alert(data.message || 'Payment verification failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred. Please try again.");
            });
        },
        "prefill": {
            "name": "{{ auth()->user()->name }}",
            "email": "{{ auth()->user()->email }}"
        },
        "theme": {
            "color": "#198754"
        }
    };

    const rzp = new Razorpay(options);
    rzp.open();
});
</script>


@endsection
