@extends('Layout.app')

@section('title', 'Dashboard')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-12">
        @include('Layout.msgStatus')
        <div class="row">
            <div class="col-lg-5">
                <div class="card mb-5">
                    <div class="card-header text-bg-dark">
                        Keys Registration
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped table-hover text-center">
                                @if ($keys->isNotEmpty())
                                    @foreach ($keys as $key)
                                        <tr>
                                            <td><span class="align-middle badge text-dark">{{ ($keys->currentPage() - 1) * $keys->perPage() + $loop->iteration }}</span></td>
                                            <td><span class="align-middle badge text-{{ Controller::statusColor($key->app->status) }}">{{ $key->app->name }}</span></td>
                                            <td><span class="align-middle badge text-{{ Controller::statusColor($key->status) }}">{{ Controller::censorText($key->key) }}</span></td>
                                            <td><span class="align-middle badge text-dark">{{ $key->duration }} Days</span></td>
                                            <td><span class="align-middle badge text-dark">{{ Controller::timeElapsed($key->created_at) }}</span></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6"><span class="align-middle badge text-danger fs-6">No Keys Where Found</span></td>
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
            <div class="col-lg-4">
                <div class="card mb-5">
                    <div class="card-header text-bg-dark">
                        Apps Registration
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped table-hover text-center">
                                @if ($apps->isNotEmpty())
                                    @foreach ($apps as $app)
                                        <tr>
                                            <td><span class="align-middle badge text-dark">{{ ($apps->currentPage() - 1) * $apps->perPage() + $loop->iteration }}</span></td>
                                            <td><span class="align-middle badge text-{{ Controller::statusColor($app->status) }}">{{ $app->name }}</span></td>
                                            <td><span class="align-middle badge text-dark">{{ number_format($app->ppd_basic) }}{{ $currency }}</span></td>
                                            <td><span class="align-middle badge text-dark">{{ number_format($app->ppd_premium) }}{{ $currency }}</span></td>
                                            <td><span class="align-middle badge text-dark">{{ Controller::timeElapsed($app->created_at) }}</span></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5"><span class="align-middle badge text-danger fs-6">No Apps Where Found</span></td>
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
                    <div class="card-header text-bg-dark">
                        Information
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
                                <span id="login-timer" class="badge text-dark" data-logintime="{{ session('login_time') ? session('login_time')->toIso8601String() : null }}"></span>
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
            const loginTime = new Date(loginTimeStr).getTime();


            const now = new Date();
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

            return diff;
        }

        let diff = updateLoginTime();

        let interval = diff < 60 ? 1000 : 30000;
        setInterval(updateLoginTime, interval);
    </script>
@endsection