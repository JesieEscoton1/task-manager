<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Stripe Payment Methods (Test)</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    .StripeElement { padding: 10px 12px; border: 1px solid #ced4da; border-radius: .25rem; background-color: white; }
    .StripeElement--focus { box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25); }
  </style>
</head>
<body style="background:#f3f4f4">
  @include('layouts.sidebar')
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h4 class="mb-3">Add a payment method</h4>
            <form id="setup-form">
              <label class="form-label">Card</label>
              <div id="card-element" class="form-control"></div>
              <div id="error-message" class="text-danger mt-2" role="alert"></div>
              <button id="submit" class="btn btn-primary mt-3" type="submit">Save Card</button>
            </form>
            <div id="success" class="alert alert-success mt-3 d-none">Payment method saved!</div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h4 class="mb-3">Saved payment methods</h4>
            <ul id="cards" class="list-group">
              @forelse($paymentMethods as $pm)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span>**** **** **** {{ $pm->card->last4 }} — {{ strtoupper($pm->card->brand) }} — exp {{ $pm->card->exp_month }}/{{ $pm->card->exp_year }}</span>
                  <button class="btn btn-sm btn-outline-danger" data-id="{{ $pm->id }}">Remove</button>
                </li>
              @empty
                <li class="list-group-item">No saved cards</li>
              @endforelse
            </ul>
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

    const setupForm = document.getElementById('setup-form');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success');

    setupForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      errorMessage.textContent = '';
      successMessage.classList.add('d-none');
      try {
        const resp = await fetch("{{ route('stripe.setup-intent') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        });
        const data = await resp.json();
        if (!resp.ok) {
          throw new Error(data.message || 'Failed to create SetupIntent');
        }
        const { error } = await stripe.confirmCardSetup(data.clientSecret, {
          payment_method: { card: cardElement }
        });
        if (error) {
          errorMessage.textContent = error.message || 'Setup failed';
        } else {
          successMessage.classList.remove('d-none');
          setTimeout(() => window.location.reload(), 800);
        }
      } catch (err) {
        errorMessage.textContent = err.message || 'Unexpected error';
      }
    });

    document.querySelectorAll('#cards button[data-id]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const id = btn.getAttribute('data-id');
        const resp = await fetch(`{{ url('/stripe/payment-method') }}/${id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        if (resp.ok) {
          window.location.reload();
        } else {
          alert('Failed to remove');
        }
      });
    });
  </script>
</body>
</html>


