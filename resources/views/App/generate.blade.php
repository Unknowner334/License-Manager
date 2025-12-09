@extends('Layout.app')

@section('title', 'Apps')

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header text-center text-white bg-dark">
                Apps Registering
            </div>
            <div class="card-body">
                <form action={{ route('apps.generate.post') }} method="post" id="generateForm">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">App Name</label>
                                <input type="text" name="name" id="name" class="form-control" required placeholder="App Name">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">-- Select Status --</option>
                                    <option value="Active" class="text-success" selected>Active</option>
                                    <option value="Inactive" class="text-danger">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="text" name="price" id="price" class="form-control" required placeholder="Price Per Month">
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary" id="generateBtn">Register App</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('apps.index') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Apps</small></a>
        </p>
    </div>

    <script>
        document.getElementById('generateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to register an app?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, register'
            }).then((result) => {
                if (result.isConfirmed) {
                    Toast.fire({
                        icon: 'info',
                        title: 'Please wait...'
                    })

                    $('#generateForm').trigger('submit');
                }
            });
        });

        $('#generateForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('apps.generate.post') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == 0) {
                        showPopup('Success', response.message);
                    } else {
                        showPopup('Error', response.message)
                    }
                },
                error: function (xhr) {
                    showPopup('Error', xhr.responseJSON.message);
                }
            });
        });
    </script>
@endsection