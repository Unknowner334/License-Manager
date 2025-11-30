@extends('Layout.app')

@section('title', 'Apps')

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span class="h6 mb-0">Apps Registering</span>
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

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="basic" class="form-label">Basic Price</label>
                                <input type="text" name="basic" id="basic" class="form-control" required placeholder="Basic Price">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="premium" class="form-label">Premium Price</label>
                                <input type="text" name="premium" id="premium" class="form-control" required placeholder="Premium Price">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmGenerateModal"><i class="bi bi-plus-square"></i> Generate</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('apps') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Apps</small></a>
        </p>
    </div>

    <div class="modal fade" id="confirmGenerateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-bg-danger">
                    <h5 class="modal-title">Confirm Generate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to generate the app?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmGenerateBtn">Yes, Generate</button>
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