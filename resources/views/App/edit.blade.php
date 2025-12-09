@extends('Layout.app')

@section('title', 'Apps')

@section('content')
    <div class="col-lg-7">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header text-center text-white bg-dark">
                App Editing · {{ $app->name }}
            </div>
            <div class="card-body">
                <form action={{ route('apps.edit.post') }} method="post" id="updateForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" required value="{{ $app->edit_id }}">

                    <div class="form-group mb-3">
                        <label for="id" class="form-label">App ID</label>
                        <input type="text" name="id" id="id" class="form-control" required placeholder="App ID (Leave Empty for Random)" value="{{ $app->app_id }}">
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">App Name</label>
                                <input type="text" name="name" id="name" class="form-control" required placeholder="App Name" value="{{ $app->name }}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">-- Select Status --</option>
                                    <option value="Active" class="text-success" @if ($app->status == 'Active') selected @endif>Active</option>
                                    <option value="Inactive" class="text-danger" @if ($app->status == 'Inactive') selected @endif>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="text" name="price" id="price" class="form-control" required placeholder="Price Per Month" value="{{ $app->price }}">
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary mt-2" id="updateBtn">Edit App</button>

                        <button type="button" class="btn btn-outline-secondary mt-2" id="deleteBtn">Delete App</button>

                        <button type="button" class="btn btn-outline-secondary mt-2" id="deleteLicensesBtn">Delete App's Licenses</button>
                        
                        <button type="button" class="btn btn-outline-secondary mt-2" id="deleteLicensesMeBtn">Delete User Licenses</button>
                    </div>
                </form>
                <form action={{ route('apps.delete') }} method="post" id="deleteForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" required value="{{ $app->edit_id }}">
                </form>
                <form action={{ route('apps.delete.licenses') }} method="post" id="deleteLicensesForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" required value="{{ $app->edit_id }}">
                </form>
                <form action={{ route('apps.delete.licenses.me') }} method="post" id="deleteLicensesMeForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" required value="{{ $app->edit_id }}">
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('apps.index') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Apps</small></a>
        </p>
    </div>

    <script>
        document.getElementById('updateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit the app?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, edit'
            }).then((result) => {
                if (result.isConfirmed) {
                    Toast.fire({
                        icon: 'info',
                        title: 'Please wait...'
                    })

                    $('#updateForm').trigger('submit');
                }
            });
        });

        $('#updateForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('apps.edit.post') }}",
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
                text: "Are you sure you want to delete the app?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    Toast.fire({
                        icon: 'info',
                        title: 'Please wait...'
                    })

                    $('#deleteForm').trigger('submit');
                }
            });
        });

        $('#deleteForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('apps.delete') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == 0) {
                        const msg = showMessage('Success', response.message);
                        msg.then(() => {
                            window.location.href = "{{ route('apps.index') }}"
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

        document.getElementById('deleteLicensesBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to delete all the licenses from the app?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    Toast.fire({
                        icon: 'info',
                        title: 'Please wait...'
                    })

                    $('#deleteLicensesForm').trigger('submit');
                }
            });
        });

        $('#deleteLicensesForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('apps.delete.licenses') }}",
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

        document.getElementById('deleteLicensesMeBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to delete all your licenses from the app?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    Toast.fire({
                        icon: 'info',
                        title: 'Please wait...'
                    })

                    $('#deleteLicensesMeForm').trigger('submit');
                }
            });
        });

        $('#deleteLicensesMeForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('apps.delete.licenses.me') }}",
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