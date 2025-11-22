@extends('Layout.app')

@section('title', 'Referrables Code')

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span class="h6 mb-0">Reff Registering</span>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-outline-light btn-sm" href={{ route('admin.referrable') }}><i class="bi bi-person-add"></i> BACK</a>
                </div>
            </div>
            <div class="card-body">
                <form action={{ route('admin.referrable.generate.post') }} method="post" id="generateForm">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="code" class="form-label">Code</label>
                        <input type="text" name="code" id="code" class="form-control" placeholder="Code (Leave Empty for random 16 chars)" max="50">
                    </div>

                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">-- Select Status --</option>
                            <option value="Active" selected>Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmGenerateModal"><i class="bi bi-plus-square"></i> Register Reff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmGenerateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-bg-danger">
                    <h5 class="modal-title">Confirm Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to register the referrable code?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmGenerateBtn">Yes, Register</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('confirmGenerateBtn').addEventListener('click', function() {
            document.getElementById('generateForm').submit();
        });
    </script>
@endsection