@extends('Layout.app')

@section('title', 'Web UI Settings')

@section('content')
    <div class="col-lg-7">
        @include('Layout.msgStatus')
        <div class="card shadow-sm">
            <div class="card-header text-center text-white bg-dark">
                Web UI Settings
            </div>
            <div class="card-body">
                <form action="#" method="post" id="updateForm">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="app_name" class="form-label">APP Name</label>
                                <input type="text" name="app_name" id="app_name" class="form-control" required placeholder="APP Name" value="{{ config('app.name') }}">
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="app_timezone" class="form-label">APP Timezone</label>
                                <input type="text" name="app_timezone" id="app_timezone" class="form-control" required placeholder="APP Timezone" value="{{ config('app.timezone') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <input type="text" name="currency" id="currency" class="form-control" required placeholder="Currency" value="{{ config('messages.settings.currency') }}">
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="currency_place" class="form-label">Currency Place</label>
                                <select name="currency_place" id="currency_place" class="form-control">
                                    @php $place = config('messages.settings.currency_place'); @endphp
                                    <option value="0" @if ($place == 0) selected @endif>Before The price</option>
                                    <option value="1" @if ($place == 1) selected @endif>After the price</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary" id="updateBtn">Update WebUI Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('updateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit the WebUI? This operation is irreversable!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('updateForm').submit();
                }
            });
        });
    </script>
@endsection