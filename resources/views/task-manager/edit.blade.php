<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Task Manager</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body style="background:#f3f4f4">
    @include('layouts.sidebar')
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12 margin-tb">
                        <div class="pull-left mb-2">
                            <h2>Edit Task Manager</h2>
                        </div>
                    </div>
                </div>
                @foreach($data as $task)
                <form id="add-taskManager">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                        <input type="hidden" name="taskId" id="taskId" class="form-control" value="{{ $task->id }}">
                            <div class="form-group">
                                <strong>Title</strong>
                                <input type="text" name="title" id="title" class="form-control" value="{{ $task->title }}">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Description</strong>
                                <input type="text" name="description" id="description" class="form-control" value="{{ $task->description }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary ml-3">Submit</button>
                    </div>
                </form>
                @endforeach
            </div>
        </div>
    </div>
</body>

</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
$(document).ready(function() {
    console.log('test');

    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    $('#add-taskManager').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            method: 'POST',
            url: "{{ route('update') }}",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '<h4>Task Manager Successfully Updated!</h4>',
                    showConfirmButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('index')}}";
                    }
                });
            },
        });
    });
});
</script>