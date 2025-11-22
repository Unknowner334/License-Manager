@extends('Layout.app')

@section('title', 'Referrables Code')

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span class="h6 mb-0">Reff Editing</span>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-outline-light btn-sm" href={{ route('admin.referrable') }}><i class="bi bi-person-add"></i> BACK</a>
                </div>
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
                            <option value="Active" @if ($reff->status == "Active") selected @endif>Active</option>
                            <option value="Inactive" @if ($reff->status == "Inactive") selected @endif>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmUpdateModal"><i class="bi bi-plus-square"></i> Update Reff</button>
                    
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="bi bi-trash3"></i> Delete Reff</button>
                    </div>
                </form>
                <form action="{{ route('admin.referrable.delete') }}" method="post" id="deleteForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" value="{{ $reff->edit_id }}">
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
                    Are you sure you want to update the referrable code?
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
                    Are you sure you want to delete the referrable?
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