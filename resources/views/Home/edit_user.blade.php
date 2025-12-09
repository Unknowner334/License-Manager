@extends('Layout.app')

@section('title', 'Users')

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card shadow-sm mb-5">
            <div class="card-header text-center text-white bg-dark">
                Users Editing · {{ $user->username }}
            </div>
            <div class="card-body">
                <form action={{ route('admin.users.edit.post') }} method="post" id="editForm">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" required value="{{ $user->user_id }}">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" required placeholder="Name" value="{{ $user->name }}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" required placeholder="Username" value="{{ $user->username }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="password" class="form-check-label form-label">
                                    New Password
                                    <input type="checkbox" name="new_password" id="new_password" class="form-check-input" value=1>
                                </label>
                                <input type="password" name="password" id="password" class="form-control" required placeholder="Password">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Password">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">-- Select Status --</option>
                                    <option value="Active" class="text-success" @if ($user->status == "Active") selected @endif>Active</option>
                                    <option value="Inactive" class="text-danger" @if ($user->status == "Inactive") selected @endif>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="role" class="form-control">
                                    <option value="">-- Select Role --</option>
                                    <option value="Owner" class="text-danger" @if ($user->role == "Owner") selected @endif>Owner</option>
                                    <option value="Manager" class="text-warning" @if ($user->role == "Manager") selected @endif>Manager</option>
                                    <option value="Reseller" class="text-primary" @if ($user->role == "Reseller") selected @endif>Reseller</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary" id="editBtn">Edit User</button>

                        <button type="button" class="btn btn-outline-secondary" id="deleteBtn"><i class="bi bi-trash3"></i> Delete User</button>
                    </div>
                </form>
                <form action="{{ route('admin.users.delete') }}" method="post" id="deleteForm">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" value="{{ $user->user_id }}">
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('admin.users.index') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Users</small></a>
        </p>
    </div>

    <script>
        document.getElementById('editBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit the user?",
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

                    $('#editForm').trigger('submit');
                }
            });
        });

        $('#editForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.users.edit.post') }}",
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
                text: "Are you sure you want to delete the user?",
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
                url: "{{ route('admin.users.delete') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == 0) {
                        const msg = showMessage('Success', response.message);
                        msg.then(() => {
                            window.location.href = "{{ route('admin.users.index') }}"
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