@extends('Layout.app')

@section('title', 'Login')

@section('content')
    <div class="col-lg-3 col-md-4 col-sm-5">
        @include('Layout.msgStatus')
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="h6 mb-0">Login</span>
            </div>
            <div class="card-body">
                <form action="{{ route('login.post') }}" method="post">
                    @csrf
                    @honeypot
                    <div class="form-group mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Your Username" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Your Password" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="stay_log" class="form-check-label">
                            <input type="checkbox" name="stay_log" id="stay_log" class="form-check-input" value=1>
                            Remember me?
                        </label>
                    </div>

                    <div class="form-group mb-2">
                        <button type="submit" class="btn btn-outline-dark"><i class="bi bi-box-arrow-in-right"></i> Login</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="text-center text-muted after-card" style="">
            <small class="bg-white px-auto p-2 rounded">
                Don't have an account yet?
                <a href="{{ route('register') }}" class="text-dark">Register here</a>
            </small>
        </p>
    </div>
@endsection