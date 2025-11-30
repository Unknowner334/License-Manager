@extends('Layout.app')

@section('title', 'Dashboard')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-12">
        @include('Layout.msgStatus')
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-5">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span class="h6 mb-0">Keys Registration</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-sm table-bordered table-hover text-center">
                                @if ($keys->isNotEmpty())
                                    @foreach ($keys as $key)
                                        <tr>
                                            <td><span class="align-middle badge fw-semibold text-dark">{{ ($keys->currentPage() - 1) * $keys->perPage() + $loop->iteration }}</span></td>
                                            <td><span class="align-middle badge fw-semibold text-{{ Controller::statusColor($key->app->status) }}">{{ $key->app->name }}</span></td>
                                            <td><span class="align-middle badge fw-semibold text-{{ Controller::statusColor($key->status) }}">{{ Controller::censorText($key->key) }}</span></td>
                                            <td><span class="align-middle badge fw-semibold text-dark">{{ $key->duration }} Days</span></td>
                                            <td><i class="align-middle badge fw-normal text-muted">{{ Controller::timeElapsed($key->created_at) }}</i></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6"><span class="align-middle badge fw-normal text-danger fs-6">No <strong>Keys</strong> Where Found</span></td>
                                    </tr>
                                @endif
                            </table>
                        </div>

                        <div class="d-flex justify-content-end">
                            {{ $keys->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card mb-5">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span class="h6 mb-0">Apps Registration</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-sm table-bordered table-hover text-center">
                                @if ($apps->isNotEmpty())
                                    @foreach ($apps as $app)
                                        <tr>
                                            <td><span class="align-middle badge fw-semibold text-dark">{{ ($apps->currentPage() - 1) * $apps->perPage() + $loop->iteration }}</span></td>
                                            <td><span class="align-middle badge fw-semibold text-{{ Controller::statusColor($app->status) }}">{{ $app->name }}</span></td>
                                            <td><span class="align-middle badge fw-semibold text-dark">{{ number_format($app->ppd_basic) }}{{ $currency }}</span></td>
                                            <td><span class="align-middle badge fw-semibold text-dark">{{ number_format($app->ppd_premium) }}{{ $currency }}</span></td>
                                            <td><i class="align-middle badge fw-normal text-muted">{{ Controller::timeElapsed($app->created_at) }}</i></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5"><span class="align-middle badge fw-normal text-danger fs-6">No <strong>Apps</strong> Where Found</span></td>
                                    </tr>
                                @endif
                            </table>
                        </div>

                        <div class="d-flex justify-content-end">
                            {{ $apps->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card mb-5">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <span class="h6 mb-0">Information</span>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-hover mb-3">
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Name
                                <span class="badge text-dark">{{ auth()->user()->name }}</span>
                            </li>
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Permissions
                                <span class="badge text-dark">{{ auth()->user()->permissions }}</span>
                            </li>
                        </ul>
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Login Time
                                <i id="login-timer" class="badge text-muted" data-logintime="{{ $loginTime ? $loginTime->toIso8601String() : null }}"></i>
                            </li>
                        </ul>
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Auto Logout
                                <i id="login-timer" class="badge text-muted">{{ $expiryTime }}</i>
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

        function startLoginTimer() {
            const interval = updateLoginTime();
            setTimeout(startLoginTimer, interval);
        }

        startLoginTimer();
    </script>
@endsection