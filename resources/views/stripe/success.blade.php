<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payment Success</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    .receipt-card { border: 2px solid #28a745; }
    .success-icon { color: #28a745; font-size: 4rem; }
  </style>
</head>
<body style="background:#f3f4f4">
  @include('layouts.sidebar')
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card receipt-card">
          <div class="card-body text-center">
            <div class="success-icon mb-3">
              <i class="bx bx-check-circle"></i>
            </div>
            <h2 class="text-success mb-4">Payment Successful!</h2>
            
            @if(isset($amount) && isset($currency))
              <div class="alert alert-success">
                <h4>Receipt</h4>
                <p class="mb-2"><strong>Amount:</strong> {{ $currency }} {{ number_format($amount, 2) }}</p>
                <p class="mb-2"><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($status) }}</span></p>
                <p class="mb-2"><strong>Transaction ID:</strong> <code>{{ $intent->id }}</code></p>
                <p class="mb-2"><strong>Date:</strong> {{ \Carbon\Carbon::createFromTimestamp($intent->created)->format('M d, Y H:i:s') }}</p>
                
                @if(isset($card))
                  <p class="mb-2"><strong>Card:</strong> **** **** **** {{ $card->last4 }} ({{ strtoupper($card->brand) }})</p>
                @endif
              </div>
            @else
              <div class="alert alert-info">
                <h4>Setup Complete</h4>
                <p class="mb-2"><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($status) }}</span></p>
                <p class="mb-2"><strong>Setup ID:</strong> <code>{{ $intent->id }}</code></p>
                <p class="mb-2"><strong>Date:</strong> {{ \Carbon\Carbon::createFromTimestamp($intent->created)->format('M d, Y H:i:s') }}</p>
                
                @if(isset($card))
                  <p class="mb-2"><strong>Card:</strong> **** **** **** {{ $card->last4 }} ({{ strtoupper($card->brand) }})</p>
                @endif
              </div>
            @endif
            
            <div class="mt-4">
              <a href="{{ route('stripe.index') }}" class="btn btn-primary me-2">Make Another Payment</a>
              <a href="{{ route('stripe.methods') }}" class="btn btn-outline-primary">Manage Payment Methods</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
