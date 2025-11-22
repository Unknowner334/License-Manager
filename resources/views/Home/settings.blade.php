@extends('Layout.app')

@section('title', 'Settings')

@section('content')
    <div class="col-lg-12">
        @include('Layout.msgStatus')
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-5">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="h6 mb-0">Change Username</span>
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
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmUsernameModal">Change Username</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-5">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span class="h6 mb-0">Change Name</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <form action={{ route('settings.name') }} method="post" id="nameForm">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="username" class="form-label">New Username</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Your New Name" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Your Password" required>
                                </div>

                                <div class="form-group">
                                    <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#confirmNameModal">Change Name</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-5">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <span class="h6 mb-0">Change Password</span>
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
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmPasswordModal">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmUsernameModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white bg-danger">
                    <h5 class="modal-title">Confirm Username Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update the username?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmUsernameBtn">Yes, Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmNameModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white bg-dark">
                    <h5 class="modal-title">Confirm Name Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update the name?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-dark" id="confirmNameBtn">Yes, Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-white bg-danger">
                    <h5 class="modal-title">Confirm Password Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update the password?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmPasswordBtn">Yes, Update</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('confirmUsernameBtn').addEventListener('click', function() {
            document.getElementById('usernameForm').submit();
        });

        document.getElementById('confirmNameBtn').addEventListener('click', function() {
            document.getElementById('nameForm').submit();
        });

        document.getElementById('confirmPasswordBtn').addEventListener('click', function() {
            document.getElementById('passwordForm').submit();
        });
    </script>
@endsection