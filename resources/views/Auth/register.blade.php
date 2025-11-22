@extends('Layout.app')

@section('title', 'Register')

@section('content')
    <div class="col-lg-3 col-md-4 col-sm-5">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="h6 mb-0">Register</span>
            </div>
            <div class="card-body">
                <form action="{{ route('register.post') }}" method="post">
                    @csrf
                    @honeypot
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control" required placeholder="Your Name">
                    </div>

                    <div class="form-group mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required placeholder="Your Username">
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Your Password">
                    </div>

                    <div class="form-group mb-3">
                        <label for="password_confirmation" class="form-label">Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Confirm Password">
                    </div>

                    <div class="form-group mb-3">
                        <label for="reff" class="form-label">Referrable Code</label>
                        <input type="text" name="reff" id="reff" class="form-control" required placeholder="Referrable Code">
                    </div>

                    <div class="form-group mb-2">
                        <button type="submit" class="btn btn-outline-dark"><i class="bi bi-box-arrow-in-right"></i> Register</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="text-center text-muted after-card" style="">
            <small class="bg-white px-auto p-2 rounded">
                Already have an account yet?
                <a href="{{ route('login') }}" class="text-dark">Login here</a>
            </small>
        </p>
    </div>
@endsection