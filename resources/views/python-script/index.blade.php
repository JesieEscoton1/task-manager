<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Run Python Script</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
</head>

<body>
    @include('layouts.sidebar')

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

    @if(isset($dnsRecords))
    <div class="container mt-2">
        <div class="card mt-4">
            <div class="card-body">
                <div class="card-datatable table-responsive">
                    <div class="row">
                        <div class="col-md-3">
                            <h2>DNS Records</h2>
                        </div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3">
                            <div style="float: right;">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#add-dns">ADD DNS RECORD</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered" id="data-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Content</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dnsRecords as $record)
                            <tr>
                                <td>{{ $record['type'] }}</td>
                                <td>{{ $record['name'] }}</td>
                                <td>{{ $record['content'] }}</td>
                                <td>
                                <a href="javascript:;" class="edit" data-toggle="modal" data-target="#edit-dns" data-id="{{ $record['id'] }}"><i class="bx bx-edit"></i></a>
                                    <a href="javascript:;" class="preview" data-toggle="modal" data-target="#recordDetailsModal" data-details="{{ json_encode($record) }}"><i class="bx bx-show"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif


    <!-- Modal Add -->
    <div class="modal fade" id="add-dns" tabindex="-1" aria-labelledby="modal-service" aria-hidden="true">
        <div class="modal-dialog d-flex justify-content-center">
            <div class="modal-content w-75">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-service">Add DNS Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="add-record">
                        <div class="form-outline mb-4">
                            <label class="form-label" for="type">Type</label>
                            <input type="text" id="type" name="type" class="form-control" />
                        </div>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" />
                        </div>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="content">Content</label>
                            <input type="text" id="content" name="content" class="form-control" />
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->

    <!-- Modal to display details -->
    <div class="modal fade" id="recordDetailsModal" tabindex="-1" aria-labelledby="modal-record-details" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-record-details">DNS Record Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre id="recordDetails"></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit DNS Modal -->
    <div class="modal fade" id="edit-dns" tabindex="-1" aria-labelledby="modal-edit-dns" aria-hidden="true">
        <div class="modal-dialog d-flex justify-content-center">
            <div class="modal-content w-75">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-edit-dns">Edit DNS Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="edit-record">
                    <input type="hidden" id="edit-record-id" name="edit-record-id" value="">
                        <div class="form-outline mb-4">
                            <label class="form-label" for="edit-type">Type</label>
                            <input type="text" id="edit-type" name="edit-type" class="form-control" />
                        </div>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="edit-name">Name</label>
                            <input type="text" id="edit-name" name="edit-name" class="form-control" />
                        </div>
                        <div class="form-outline mb-4">
                            <label class="form-label" for="edit-content">Content</label>
                            <input type="text" id="edit-content" name="edit-content" class="form-control" />
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    $('#data-table').DataTable();

    $('.preview').on('click', function () {
        var details = $(this).data('details');
        $('#recordDetails').text(JSON.stringify(details, null, 2));
        $('#recordDetailsModal').modal('show');
    });

    // Edit DNS Record
    $('.edit').on('click', function () {
        var recordId = $(this).data('id');
        $.ajax({
            url: '/get-dns-record/' + recordId, // Create a route for fetching DNS record details by ID
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                populateEditModal(response);
            },
            error: function(error) {
                console.error('Error fetching DNS record details:', error);
            },
        });
    });

    function populateEditModal(record) {
        // Populate the fields in the edit modal with the record details
        $('#edit-record-id').val(record.id);
        $('#edit-type').val(record.type);
        $('#edit-name').val(record.name);
        $('#edit-content').val(record.content);

        // Show the edit modal
        $('#edit-dns').modal('show');
    }

    // Submit Edit Form
    $('#edit-record').on('submit', function(e) {
        e.preventDefault();

        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        var formData = new FormData(this);

        $.ajax({
            url: '{{ route("updateDnsRecord") }}', // Create a route for updating DNS records
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '<h4>DNS Successfully Updated!</h4>',
                    showConfirmButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('runPython')}}";
                    }
                });
            },
            error: function(error) {
                console.error('Error updating DNS record:', error);
            },
        });
    });

});

$(document).ready(function() {
    $('#add-record').on('submit', function(e) {
        e.preventDefault();

        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        var formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("addDnsRecord") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: '<h4>DNS Successfully Added!</h4>',
                    showConfirmButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('runPython')}}";
                    }
                });
            },
            error: function(error) {
                console.error('Error adding DNS record:', error);
            },
        });
    });
});
</script>