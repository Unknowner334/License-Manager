@extends('Layout.app')

@section('title', 'Licenses')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card shadow-sm mb-5">
            <div class="card-header text-center text-white bg-dark">
                License Registering
            </div>
            <div class="card-body">
                <form action="{{ route('licenses.generate.post') }}" method="POST" id="generateForm">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="app" class="form-label">App</label>
                                <select name="app" id="app" class="form-control">
                                    <option value="">-- Select App --</option>
                                    @php $count = 0; @endphp
                                    @if ($apps)
                                        @foreach ($apps as $app)
                                            @php $count += 1; @endphp
                                            @php if ($currencyPlace == 0) $price = number_format($app->price) . $currency; else $price = $currency . number_format($app->price); @endphp
                                            <option value="{{ $app->app_id }}" @if ($count == 1) selected @endif>{{ $app->name }} - {{ $price }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="owner" class="form-label">Owner's Full Name</label>
                                <input type="text" name="owner" id="owner" class="form-control" required placeholder="Owner's Full Name">
                            </div>
                        </div>
                    </div>

                    <div class="row">
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

                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="duration" class="form-label">Duration</label>
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
                                            <option value="{{ $duration*30 }}" @if ($duration*30 == 30) selected @endif>{{ $duration*30 }} Days, {{ $duration }} Months</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="devices" class="form-label">Max Devices</label>
                                <input type="number" name="devices" id="devices" class="form-control" required placeholder="Max Devices" value="1">
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="price" class="form-label">License Price</label>
                                <input type="text" id="price" class="form-control" style="background-color: rgb(233, 236, 239); opacity: 1;" placeholder="The license will cost" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="saldo" class="form-label">Saldo Cut</label>
                        <input type="text" id="saldo" class="form-control" style="background-color: rgb(233, 236, 239); opacity: 1;" placeholder="Your order will total" readonly>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-secondary" id="generateBtn">Register License</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('licenses.index') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Licenses</small></a>
        </p>
    </div>

    <script>
        document.getElementById('generateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to register a license?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, register'
            }).then((result) => {
                if (result.isConfirmed) {
                    Toast.fire({
                        icon: 'info',
                        title: 'Please wait...'
                    })

                    $('#generateForm').trigger('submit');
                }
            });
        });

        $('#generateForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('licenses.generate.post') }}",
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

        function updateSaldoCutEstimation() {
            const estimationElem = document.getElementById('saldo');
            if (!estimationElem) return;

            const duration = parseInt(document.getElementById('duration').value, 10);
            const devices = parseInt(document.getElementById('devices').value, 10);

            if (isNaN(duration) || isNaN(devices)) {
                estimationElem.value = "Fill all fields";
                return;
            }

            const basePrice = 10;
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
        document.getElementById('duration').addEventListener('change', updateSaldoCutEstimation);
        document.getElementById('devices').addEventListener('input', updateSaldoCutEstimation);

        updateLicenseGenerateEstimation();
        updateSaldoCutEstimation();
    </script>
@endsection