@extends('Layout.app')

@section('title', 'Keys')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header text-center text-white bg-dark">
                Key Editing · {{ $key->key }}
            </div>
            <div class="card-body">
                <form action={{ route('keys.edit.post') }} method="post" id="updateForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" value="{{ $key->edit_id }}" required>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="key" class="form-label">Key (Leave Empty For Random Key)</label>
                                <input type="text" name="key" id="key" class="form-control" placeholder="Key" value="{{ $key->key }}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="devices" class="form-label">Max Devices</label>
                                <input type="number" name="devices" id="devices" class="form-control" required placeholder="Max Devices" value="{{ $key->max_devices }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="app" class="form-label">App</label>
                                <select name="app" id="app" class="form-control">
                                    <option value="">-- Select App --</option>
                                    @if ($apps)
                                        @foreach ($apps as $app)
                                            <option value="{{ $app->app_id }}" @if ($app->app_id == $key->app_id) selected @endif>{{ $app->name }} - {{ number_format($app->price) . $currency }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="owner" class="form-label">Owner's Full Name</label>
                                <input type="text" name="owner" id="owner" class="form-control" required placeholder="Owner's Full Name" value="{{ $key->owner }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">-- Select Status --</option>
                                    <option value="Active" class="text-success" @if ($key->status == "Active") selected @endif>Active</option>
                                    <option value="Inactive" class="text-danger" @if ($key->status == "Inactive") selected @endif>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="duration" class="form-check-label form-label">
                                    Duration Update
                                    <input type="checkbox" name="duration-update" id="duration-update" class="form-check-input" value=1>
                                </label>
                                <select name="duration" id="duration" class="form-control">
                                    @php
                                        $duration_list = [];
                                        foreach (range(1, 48) as $d) {
                                            $duration_list[] = $d;
                                        }
                                    @endphp
                                    <option value="">-- Select Duration --</option>
                                    @if(!empty($duration_list))
                                        @foreach($duration_list as $duration)
                                            <option value="{{ $duration*30 }}" @if ($duration*30 == $key->duration) selected @endif>{{ $duration*30 }} Days, {{ $duration }} Months</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="estimation" class="form-label">Estimation</label>
                        <input type="text" id="estimation" class="form-control" style="background-color: rgb(233, 236, 239); opacity: 1;" placeholder="Your order will total" readonly>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-dark" id="updateBtn">Update</button>

                        <button type="button" class="btn btn-outline-dark" id="deleteBtn"><i class="bi bi-trash3"></i> Delete</button>
                    </div>
                </form>
                <form action="{{ route('keys.delete') }}" method="post" id="deleteForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" value="{{ $key->edit_id }}">
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('keys') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Keys</small></a>
        </p>
    </div>

    <script>
        document.getElementById('updateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit the key?",
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

        document.getElementById('deleteBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to delete the key?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm').submit();
                }
            });
        });

        function numberFormat(number, decimals = 0, decPoint = '.', thousandsSep = ',') {
            number = parseFloat(number);

            if (isNaN(number)) return '0';

            let n = number.toFixed(decimals);

            let parts = n.split('.');

            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);

            return parts.join(decPoint);
        }

        const appPrices = {
            @foreach($apps as $app)
                "{{ $app->app_id }}": { price: {{ $app->price }} }{{ !$loop->last ? ',' : '' }}
            @endforeach
        };

        function updateKeyGenerateEstimation() {
            const estimationElem = document.getElementById('estimation');
            if (!estimationElem) return;

            const appId = document.getElementById('app').value;
            const duration = parseInt(document.getElementById('duration').value, 10);
            const devices = parseInt(document.getElementById('devices').value, 10);

            if (!appId || !appPrices[appId]) {
                estimationElem.value = "Select an app";
                return;
            }

            if (isNaN(duration) || isNaN(devices)) {
                estimationElem.value = "Fill all fields";
                return;
            }

            const basePrice =appPrices[appId].price;
            const multiplier = duration / 30;
            const total = basePrice * multiplier * devices;
            const totalFormatted = numberFormat(total)

            estimationElem.value = `${totalFormatted}{{ $currency }}`;
        }

        document.getElementById('app').addEventListener('change', updateKeyGenerateEstimation);
        document.getElementById('duration').addEventListener('change', updateKeyGenerateEstimation);
        document.getElementById('devices').addEventListener('input', updateKeyGenerateEstimation);

        updateKeyGenerateEstimation();
    </script>
@endsection