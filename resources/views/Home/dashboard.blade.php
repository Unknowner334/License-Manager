@extends('Layout.app')

@section('title', 'Dashboard')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-10">
        @include('Layout.msgStatus')
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header text-white bg-dark">
                        Keys Registration History
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover text-center">
                                @if ($keys->isNotEmpty())
                                    @foreach ($keys as $item)
                                        <tbody>
                                            <tr>
                                                <td><span class="align-middle badge text-dark fw-semibold">#{{ $item->id }}</span></td>
                                                <td>{{ $item->app->name }}</td>
                                                <td><span class="align-middle badge text-dark fw-semibold">{{ Controller::censorText($item->key) }}</span></td>
                                                <td><span class="align-middle badge text-dark fw-semibold">{{ $item->duration }} Days</span></td>
                                                <td><span class="align-middle badge text-primary fw-semibold">{{ Controller::userUsername($item->registrar) }}</span></td>
                                                <td><span class="align-middle badge text-primary fw-semibold">{{ $item->max_devices }} Devices</span></td>
                                                <td><i class="align-middle badge text-muted fw-semibold">{{ Controller::timeElapsed($item->created_at) }}</i></td>
                                            </tr>
                                        </tbody>
                                    @endforeach
                                @else
                                    <thead>
                                        <tr>
                                            <th><span class="align-middle badge text-dark fw-normal fs-6">There are no <strong>keys</strong> to show</span></th>
                                        </tr>
                                    </thead>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header text-center text-white bg-dark">
                        Information
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-hover mb-3">
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Name
                                <span class="badge text-dark fw-bold">{{ auth()->user()->name }}</span>
                            </li>
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Roles
                                <span class="badge text-{{ Controller::permissionColor(auth()->user()->role) }} fw-bold">{{ auth()->user()->role }}</span>
                            </li>
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Saldo
                                @php $saldo = Controller::saldoData(auth()->user()->saldo, auth()->user()->role); @endphp
                                <span class="badge text-{{ $saldo[1] }} fw-bold">{{ $saldo[0] }}</span>
                            </li>
                        </ul>
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Login Time
                                <span id="login-timer" class="badge text-dark fw-bold" data-logintime="{{ $loginTime ? $loginTime->toIso8601String() : null }}"></span>
                            </li>
                        </ul>
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Auto Logout
                                <span id="expiry-timer" class="badge text-dark fw-bold" data-expiry="{{ $expiryTime }}"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateLoginTime() {
            const timerElem = document.getElementById('login-timer');
            const loginTimeStr = timerElem.getAttribute('data-logintime');

            if (!loginTimeStr) {
                timerElem.textContent = 'never logged in';
                return 60000;
            }

            const loginTime = new Date(loginTimeStr).getTime();
            if (isNaN(loginTime)) {
                timerElem.textContent = 'invalid date';
                return 60000;
            }

            const now = Date.now();
            const diff = Math.floor((now - loginTime) / 1000);

            let display = '';
            if (diff < 60) {
                display = diff + ' seconds ago';
            } else if (diff < 3600) {
                const minutes = Math.floor(diff / 60);
                display = `${minutes} minutes ago`;
            } else if (diff < 86400) {
                const hours = Math.floor(diff / 3600);
                display = `${hours} hours ago`;
            } else {
                const days = Math.floor(diff / 86400);
                display = `${days} days ago`;
            }

            timerElem.textContent = display;

            if (diff < 60) return 1000;
            else if (diff < 3600) return 30000;
            else return 300000;
        }

        function updateExpiryTime() {
            const expiryElem = document.getElementById('expiry-timer');
            const expiryStr = expiryElem.getAttribute('data-expiry');

            if (!expiryStr) {
                expiryElem.textContent = 'no expiry';
                return 60000;
            }

            const expiryTime = new Date(expiryStr).getTime();
            if (isNaN(expiryTime)) {
                expiryElem.textContent = 'invalid expiry';
                return 60000;
            }

            const now = Date.now();
            let diff = Math.floor((expiryTime - now) / 1000);

            if (diff <= 0) {
                expiryElem.textContent = 'expired';
                return 60000;
            }

            let display = '';
            if (diff < 60) {
                display = `in ${diff} seconds`;
            } else if (diff < 3600) {
                const minutes = Math.floor(diff / 60);
                display = `in ${minutes} minutes`;
            } else if (diff < 86400) {
                const hours = Math.floor(diff / 3600);
                display = `in ${hours} hours`;
            } else {
                const days = Math.floor(diff / 86400);
                display = `in ${days} days`;
            }

            expiryElem.textContent = display;

            if (diff < 60) return 1000;
            else if (diff < 3600) return 30000;
            else return 300000;
        }

        function startExpiryTimer() {
            const interval = updateExpiryTime();
            setTimeout(startExpiryTimer, interval);
        }

        function startLoginTimer() {
            const interval = updateLoginTime();
            setTimeout(startLoginTimer, interval);
        }

        startExpiryTimer();
        startLoginTimer();
    </script>
@endsection