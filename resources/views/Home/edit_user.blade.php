@extends('Layout.app')

@section('title', 'Users')

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header text-bg-danger">
                <div class="row">
                    <div class="col pt-1">
                        User Updating
                    </div>
                    <div class="col text-end">
                        <a class="btn btn-outline-light btn-sm" href={{ route('admin.users') }}><i class="bi bi-key"></i> BACK</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action={{ route('admin.users.edit.post') }} method="post" id="updateForm">
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
                                <label for="perm" class="form-label">Permissions</label>
                                <select name="perm" id="perm" class="form-control">
                                    <option value="">-- Select Permissions --</option>
                                    <option value="Owner" class="text-danger" @if ($user->permissions == "Owner") selected @endif>Owner</option>
                                    <option value="Admin" class="text-warning" @if ($user->permissions == "Admin") selected @endif>Admin</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal"><i class="bi bi-plus-square"></i> Update User</button>

                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="bi bi-trash3"></i> Delete User</button>
                    </div>
                </form>
                <form action="{{ route('admin.users.delete') }}" method="post" id="deleteForm">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" value="{{ $user->user_id }}">
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmUpdateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-bg-danger">
                    <h5 class="modal-title">Confirm Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update the user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmUpdateBtn">Yes, Update</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-bg-danger">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('confirmUpdateBtn').addEventListener('click', function() {
            document.getElementById('updateForm').submit();
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            document.getElementById('deleteForm').submit();
        });
    </script>
@endsection