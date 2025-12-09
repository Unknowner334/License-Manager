@extends('Layout.app')

@section('title', 'Licenses')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header text-center text-white bg-dark">
                License Editing · {{ $license->license }} · {{ Controller::userUsername($license->registrar) }}
            </div>
            <div class="card-body">
                <form action={{ route('licenses.edit.post') }} method="post" id="updateForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" value="{{ $license->edit_id }}" required>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="license" class="form-label">License (Leave Empty For Random)</label>
                                <input type="text" name="license" id="license" class="form-control" placeholder="license" value="{{ $license->license }}">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="devices" class="form-label">Max Devices</label>
                                <input type="number" name="devices" id="devices" class="form-control" required placeholder="Max Devices" value="{{ $license->max_devices }}">
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
                                            @php if ($currencyPlace == 0) $price = number_format($app->price) . $currency; else $price = $currency . number_format($app->price); @endphp
                                            <option value="{{ $app->app_id }}" @if ($app->app_id == $license->app_id) selected @endif>{{ $app->name }} - {{ $price }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="owner" class="form-label">Owner's Full Name</label>
                                <input type="text" name="owner" id="owner" class="form-control" required placeholder="Owner's Full Name" value="{{ $license->owner }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">-- Select Status --</option>
                                    <option value="Active" class="text-success" @if ($license->status == "Active") selected @endif>Active</option>
                                    <option value="Inactive" class="text-danger" @if ($license->status == "Inactive") selected @endif>Inactive</option>
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
                                            <option value="{{ $duration*30 }}" @if ($duration*30 == $license->duration) selected @endif>{{ $duration*30 }} Days, {{ $duration }} Months</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="price" class="form-label">License Price</label>
                        <input type="text" id="price" class="form-control" style="background-color: rgb(233, 236, 239); opacity: 1;" placeholder="The license will cost" readonly>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary" id="updateBtn">Edit License</button>

                        <button type="button" class="btn btn-outline-secondary" id="deleteBtn"><i class="bi bi-trash3"></i> Delete License</button>
                    </div>
                </form>
                <form action="{{ route('licenses.delete') }}" method="post" id="deleteForm">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id" value="{{ $license->edit_id }}">
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('licenses.index') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Licenses</small></a>
        </p>
    </div>

    <script>
        document.getElementById('updateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to edit the license?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, edit'
            }).then((result) => {
                if (result.isConfirmed) {
                    Toast.fire({
                        icon: 'info',
                        title: 'Please wait...'
                    })

                    $('#updateForm').trigger('submit');
                }
            });
        });

        $('#updateForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('licenses.edit.post') }}",
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
                text: "Are you sure you want to delete the license?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {

                    $('#deleteForm').trigger('submit');
                }
            });
        });

        $('#deleteForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('licenses.delete') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == 0) {
                        const msg = showMessage('Success', response.message);
                        msg.then(() => {
                            window.location.href = "{{ route('licenses.index') }}"
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

        function updateLicenseGenerateEstimation() {
            const estimationElem = document.getElementById('price');
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

            const basePrice = appPrices[appId].price;
            const multiplier = duration / 30;
            const total = basePrice * multiplier * devices;
            const totalFormatted = numberFormat(total)
            let totalC = 0;
            if ({{ $currencyPlace }} == 0) {
                totalC = `${totalFormatted}{{ $currency }}`;
            } else {
                totalC = `{{ $currency }}${totalFormatted}`;
            }

            estimationElem.value = totalC;
        }

        document.getElementById('app').addEventListener('change', updateLicenseGenerateEstimation);
        document.getElementById('duration').addEventListener('change', updateLicenseGenerateEstimation);
        document.getElementById('devices').addEventListener('input', updateLicenseGenerateEstimation);

        updateLicenseGenerateEstimation();
        updateSaldoCutEstimation();
    </script>
@endsection