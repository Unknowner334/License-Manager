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
                Keys Registering
            </div>
            <div class="card-body">
                <form action={{ route('keys.generate.post') }} method="post" id="generateForm">
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
                                            <option value="{{ $app->app_id }}" @if ($count == 1) selected @endif>{{ $app->name }} - {{ number_format($app->price) . $currency }}</option>
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
                                <label for="estimation" class="form-label">Estimation</label>
                                <input type="text" id="estimation" class="form-control" style="background-color: rgb(233, 236, 239); opacity: 1;" placeholder="Your order will total" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-dark" id="generateBtn">Generate</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('keys') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Keys</small></a>
        </p>
    </div>

    <script>
        document.getElementById('generateBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to register a key?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, register'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('generateForm').submit();
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