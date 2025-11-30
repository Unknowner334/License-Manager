@extends('Layout.app')

@section('title', 'Apps')

@section('content')
    <div class="col-lg-7">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span class="h6 mb-0">App Editing</span>
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

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="basic" class="form-label">Basic Price</label>
                                <input type="text" name="basic" id="basic" class="form-control" required placeholder="Basic Price" value="{{ $app->ppd_basic }}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="premium" class="form-label">Premium Price</label>
                                <input type="text" name="premium" id="premium" class="form-control" required placeholder="Premium Price" value="{{ $app->ppd_premium }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-danger mt-2" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal"><i class="bi bi-plus-square"></i> Update</button>

                        <button type="button" class="btn btn-outline-danger mt-2" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="bi bi-trash3"></i> Delete</button>

                        <button type="button" class="btn btn-outline-danger mt-2" data-bs-toggle="modal" data-bs-target="#confirmDeleteKeysModal"><i class="bi bi-trash3"></i> Delete Only Keys</button>
                        
                        <button type="button" class="btn btn-outline-danger mt-2" data-bs-toggle="modal" data-bs-target="#confirmDeleteKeysMeModal"><i class="bi bi-trash3"></i> Delete Only User Keys</button>
                    </div>
                </form>
                <form action={{ route('apps.delete') }} method="post" id="deleteForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" required value="{{ $app->edit_id }}">
                </form>
                <form action={{ route('apps.delete.keys') }} method="post" id="deleteKeysForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" required value="{{ $app->edit_id }}">
                </form>
                <form action={{ route('apps.delete.keys.me') }} method="post" id="deleteKeysMeForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" required value="{{ $app->edit_id }}">
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('apps') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Apps</small></a>
        </p>
    </div>

    <div class="modal fade" id="confirmUpdateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-bg-danger">
                    <h5 class="modal-title">Confirm Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update the app?
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
                    Are you sure you want to delete the app?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteKeysModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-bg-danger">
                    <h5 class="modal-title">Confirm Delete Keys</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the app keys?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteKeysBtn">Yes, Delete Keys</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteKeysMeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-bg-danger">
                    <h5 class="modal-title">Confirm Delete Keys</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the app keys that are created by your user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteKeysMeBtn">Yes, Delete Keys</button>
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

        document.getElementById('confirmDeleteKeysBtn').addEventListener('click', function() {
            document.getElementById('deleteKeysForm').submit();
        });

        document.getElementById('confirmDeleteKeysMeBtn').addEventListener('click', function() {
            document.getElementById('deleteKeysMeForm').submit();
        });
    </script>
@endsection