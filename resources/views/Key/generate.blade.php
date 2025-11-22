@extends('Layout.app')

@section('title', 'Keys')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span class="h6 mb-0">Keys Registering</span>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-outline-light btn-sm" href={{ route('keys') }}><i class="bi bi-key"></i> BACK</a>
                </div>
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
                                            @php $count + 1; @endphp
                                            <option value="{{ $app->app_id }}" class="text-{{ Controller::statusColor($app->status) }}" @if ($app->status != 'Active') disabled @endif>{{ $app->name }} - {{ $app->ppd_basic }}, {{ $app->ppd_premium }}</option>
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
                                <label for="rank" class="form-label">Rank</label>
                                <select name="rank" id="rank" class="form-control">
                                    <option value="">-- Select Rank --</option>
                                    <option value="Basic" class="text-success" selected>Basic</option>
                                    <option value="Premium" class="text-warning">Premium</option>
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
                                <label for="devices" class="form-label">Max Devices</label>
                                <input type="number" name="devices" id="devices" class="form-control" required placeholder="Max Devices" value="1">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="estimation" class="form-label">Estimation</label>
                        <input type="text" id="estimation" class="form-control" style="background-color: rgb(233, 236, 239); opacity: 1;" placeholder="Your order will total" readonly>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmGenerateModal"><i class="bi bi-plus-square"></i> Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmGenerateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-bg-danger">
                    <h5 class="modal-title">Confirm Generate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to generate the key?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmGenerateBtn">Yes, Generate</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function numberFormat(number, decimals = 0, decPoint = '.', thousandsSep = ',') {
            number = parseFloat(number);

            if (isNaN(number)) return '0';

            let n = number.toFixed(decimals);

            let parts = n.split('.');

            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSep);

            return parts.join(decPoint);
        }

        document.getElementById('confirmGenerateBtn').addEventListener('click', function() {
            document.getElementById('generateForm').submit();
        });

        const appPrices = {
            @foreach($apps as $app)
                "{{ $app->app_id }}": { basic: {{ $app->ppd_basic }}, premium: {{ $app->ppd_premium }} }{{ !$loop->last ? ',' : '' }}
            @endforeach
        };

        function updateKeyGenerateEstimation() {
            const estimationElem = document.getElementById('estimation');
            if (!estimationElem) return;

            const appId = document.getElementById('app').value;
            const rankValue = document.getElementById('rank').value;
            const duration = parseInt(document.getElementById('duration').value, 10);
            const devices = parseInt(document.getElementById('devices').value, 10);

            if (!appId || !appPrices[appId]) {
                estimationElem.value = "Select an app";
                return;
            }

            if (!rankValue || isNaN(duration) || isNaN(devices)) {
                estimationElem.value = "Fill all fields";
                return;
            }

            const basePrice = (rankValue === 'Basic') ? appPrices[appId].basic : appPrices[appId].premium;
            const multiplier = duration / 30;
            const total = basePrice * multiplier * devices;
            const totalFormatted = numberFormat(total)

            estimationElem.value = `${totalFormatted}{{ $currency }}`;
        }

        document.getElementById('app').addEventListener('change', updateKeyGenerateEstimation);
        document.getElementById('rank').addEventListener('change', updateKeyGenerateEstimation);
        document.getElementById('duration').addEventListener('change', updateKeyGenerateEstimation);
        document.getElementById('devices').addEventListener('input', updateKeyGenerateEstimation);

        updateKeyGenerateEstimation();
    </script>
@endsection