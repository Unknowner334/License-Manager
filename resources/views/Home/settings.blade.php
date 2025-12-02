@extends('Layout.app')

@section('title', 'Settings')

@section('content')
    <div class="col-lg-12">
        @include('Layout.msgStatus')
        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header text-center text-white bg-danger">
                        Change Username
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <form action={{ route('settings.username') }} method="post" id="usernameForm">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="username" class="form-label">New Username</label>
                                    <input type="text" name="username" id="username" class="form-control" placeholder="Your New Username" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Your Password" required>
                                </div>

                                <div class="form-group">
                                    <button type="button" class="btn btn-outline-danger" id="usernameUpdateBtn">Change Username</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header text-center text-white bg-dark">
                        Change Name
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <form action={{ route('settings.name') }} method="post" id="nameForm">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="username" class="form-label">New Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Your New Name" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Your Password" required>
                                </div>

                                <div class="form-group">
                                    <button type="button" class="btn btn-outline-dark" id="nameUpdateBtn">Change Name</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header text-center text-white bg-danger">
                        <span class="h6 mb-0">Change Password
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <form action={{ route('settings.password') }} method="post" id="passwordForm">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Your New Password" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="currentpassword" class="form-label">Current Password</label>
                                    <input type="password" name="currentpassword" id="currentpassword" class="form-control" placeholder="Your Current Password" required>
                                </div>

                                <div class="form-group">
                                    <button type="button" class="btn btn-outline-danger" id="passwordUpdateBtn">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('nameUpdateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to change your name?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('nameForm').submit();
                }
            });
        });

        document.getElementById('usernameUpdateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to change your username?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('usernameForm').submit();
                }
            });
        });

        document.getElementById('passwordUpdateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to change your password?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('passwordForm').submit();
                }
            });
        });
    </script>
@endsection