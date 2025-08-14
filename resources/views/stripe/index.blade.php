<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Stripe Test Payment</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    .StripeElement { padding: 10px 12px; border: 1px solid #ced4da; border-radius: .25rem; background-color: white; }
    .StripeElement--focus { box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25); }
  </style>
</head>
<body style="background:#f3f4f4">
  @include('layouts.sidebar')
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h4 class="mb-3">Stripe Payment (Test)</h4>
            <div class="mb-3">
              <label for="amount" class="form-label">Amount (USD cents)</label>
              <input id="amount" class="form-control" type="number" min="50" value="1000" />
            </div>
            <form id="payment-form">
              <label class="form-label">Card</label>
              <div id="card-element" class="form-control"></div>
              <div id="error-message" class="text-danger mt-2" role="alert"></div>
              <button id="submit" class="btn btn-primary mt-3" type="submit">Pay</button>
            </form>
            <div id="success" class="alert alert-success mt-3 d-none">Payment succeeded!</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://js.stripe.com/v3/"></script>
  <script>
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');

    const paymentForm = document.getElementById('payment-form');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success');
    const amountInput = document.getElementById('amount');

    paymentForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      errorMessage.textContent = '';
      successMessage.classList.add('d-none');

      const amount = parseInt(amountInput.value || '1000', 10);

      try {
        const resp = await fetch("{{ route('stripe.intent') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ amount })
        });
        const data = await resp.json();
        if (!resp.ok) {
          throw new Error(data.message || 'Failed to create PaymentIntent');
        }

        const { error, paymentIntent } = await stripe.confirmCardPayment(data.clientSecret, {
          payment_method: { card: cardElement }
        });

        if (error) {
          errorMessage.textContent = error.message || 'Payment failed';
        } else {
          // Redirect to success page with payment intent ID
          window.location.href = "{{ route('stripe.success') }}?payment_intent=" + paymentIntent.id;
        }
      } catch (err) {
        errorMessage.textContent = err.message || 'Unexpected error';
      }
    });
  </script>
</body>
</html>


