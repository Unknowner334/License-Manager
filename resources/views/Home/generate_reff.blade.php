@extends('Layout.app')

@section('title', 'Referrables Code')

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card shadow-sm mb-5">
            <div class="card-header text-center text-white bg-dark">
                Reff Registering
            </div>
            <div class="card-body">
                <form action={{ route('admin.referrable.generate.post') }} method="post" id="registerForm">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" name="code" id="code" class="form-control" placeholder="Code (Leave Empty for random 16 chars)" max="50">
                    </div>

                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">-- Select Status --</option>
                            <option value="Active" class="text-success" selected>Active</option>
                            <option value="Inactive" class="text-danger">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary" id="registerReff">Register Reff</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('admin.referrable.index') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Referrables</small></a>
        </p>
    </div>

    <script>
        document.getElementById('registerReff').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to register the referrable?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Register'
            }).then((result) => {
                if (result.isConfirmed) {

                    $('#registerForm').trigger('submit');
                }
            });
        });

        $('#registerForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.referrable.generate.post') }}",
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
    </script>
@endsection