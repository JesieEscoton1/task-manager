<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Automation</title>
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Poppins', sans-serif; background: #f3f4f4; }
        .email-automation-page { margin-left: 260px; padding: 24px; transition: all 0.5s ease; }
        .sidebar.close ~ .email-automation-page { margin-left: 78px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
        .card-header { background: #11101d; color: #fff; font-weight: 600; border-radius: 12px 12px 0 0; padding: 14px 20px; }
        .btn-send { background: #11101d; border-color: #11101d; color: #fff; padding: 10px 24px; }
        .btn-send:hover { background: #1d1b31; border-color: #1d1b31; color: #fff; }
        .table th { font-weight: 600; color: #495057; border-top: none; }
        .table td { vertical-align: middle; }
        .alert { border-radius: 8px; }
    </style>
</head>
<body>
    @include('layouts.sidebar')

    <div class="email-automation-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0" style="color: #11101d;">
                <i class='bx bx-mail-send'></i> Email Automation
            </h4>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">Send Email</div>
            <div class="card-body">
                <form action="{{ route('email-automation.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="firstname">First name <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" id="firstname" class="form-control" value="{{ old('firstname') }}" placeholder="First name" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="lastname">Last name <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" id="lastname" class="form-control" value="{{ old('lastname') }}" placeholder="Last name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="email@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message <span class="text-danger">*</span></label>
                        <textarea name="message" id="message" class="form-control" rows="4" placeholder="Your message..." required>{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-send">
                        <i class='bx bx-send'></i> Send Email
                    </button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Sent Emails</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Message</th>
                                <th>Sent at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->first_name ." " . $record->last_name}}</td>
                                    <td>{{ $record->email }}</td>
                                    <td><span title="{{ $record->message }}">{{ Str::limit($record->message, 40) }}</span></td>
                                    <td>{{ $record->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No emails sent yet. Submit the form above to send one.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
