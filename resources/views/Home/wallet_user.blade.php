@extends('Layout.app')

@section('title', 'Users')

@section('content')
    <div class="col-lg-6">
        @include('Layout.msgStatus')
        <div class="card shadow-sm mb-5">
            <div class="card-header text-center text-white bg-danger">
                Users Saldo Editing · {{ $user->username }}
            </div>
            <div class="card-body">
                <form action={{ route('admin.users.wallet.post') }} method="post" id="changeForm">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" required value="{{ $user->user_id }}">

                    <div class="form-group mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" readonly value="{{ $user->username }}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="saldo" class="form-label">User's Saldo</label>
                        <input type="number" name="saldo" id="username" class="form-control" placeholder="User's Saldo" value="{{ $user->saldo }}" required min="1" max="2000000000">
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-outline-danger" id="changeBtn">Change User's Saldo</button>
                    </div>
                </form>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('admin.users') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Users</small></a>
        </p>
    </div>

    <script>
        document.getElementById('changeBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to change the user's saldo?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, change'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('changeForm').submit();
                }
            });
        });
    </script>
@endsection