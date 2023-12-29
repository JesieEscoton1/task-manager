<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Run Python Script</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    @include('layouts.sidebar')
    <center>

        <h1>Run Python Script</h1>
        @if(isset($output) && is_array($output))
        <div>
            <strong>Output:</strong>
            <ul>
                @foreach($output as $line)
                <li>{{ $line }}</li>
                @endforeach
            </ul>
        </div>
        @elseif(isset($output) && is_string($output))
        <div>
            <strong>Output:</strong>
            <pre>{{ $output }}</pre>
        </div>
        @elseif(isset($error))
        <div style="color: red;">
            <strong>Error:</strong> {{ $error }}
        </div>
        @endif

        <form method="post" action="{{ route('executePython') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Run Python Script</button>
        </form>
    </center>

</body>

</html>