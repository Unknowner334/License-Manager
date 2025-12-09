@extends('Layout.app')

@section('title', 'Referrables Code')

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header text-center text-white bg-dark">
                Reff Editing · {{ $reff->code }}
            </div>
            <div class="card-body">
                <form action={{ route('admin.referrable.edit.post') }} method="post" id="updateForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" value="{{ $reff->edit_id }}">

                    <div class="form-group mb-3">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" name="code" id="code" class="form-control" placeholder="Code (Leave Empty for random 16 chars)" max="50" value="{{ $reff->code }}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">-- Select Status --</option>
                            <option value="Active" class="text-success" @if ($reff->status == "Active") selected @endif>Active</option>
                            <option value="Inactive" class="text-danger" @if ($reff->status == "Inactive") selected @endif>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary" id="updateBtn">Edit Reff</button>
                    
                        <button type="button" class="btn btn-outline-secondary" id="deleteBtn"><i class="bi bi-trash3"></i> Delete Reff</button>
                    </div>
                </form>
                <form action="{{ route('admin.referrable.delete') }}" method="post" id="deleteForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" value="{{ $reff->edit_id }}">
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('admin.referrable.index') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Referrables</small></a>
        </p>
    </div>

    <script>
        document.getElementById('updateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit the referrable?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#updateForm').trigger('submit');
                }
            });
        });

        $('#updateForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.referrable.edit.post') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == 0) {
                        showPopup('Success', response.message);
                    } else {
                        showPopup('Error', response.message);
                    }
                },
                error: function (xhr) {
                    showPopup('Error', xhr.responseJSON.message);
                }
            });
        });

        document.getElementById('deleteBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to delete the referrable?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#deleteForm').trigger('submit');
                }
            });
        });

        $('#deleteForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.referrable.delete') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == 0) {
                        const $msg = showMessage('Success', response.message);
                        $msg.then(() => {
                            window.location.href = "{{ route('admin.referrable.index') }}"
                        });
                    } else {
                        showPopup('Error', response.message);
                    }
                },
                error: function (xhr) {
                    showPopup('Error', xhr.responseJSON.message);
                }
            });
        });
    </script>
@endsection