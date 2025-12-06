<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

        body {
            background-color: whitesmoke;
            font-family: 'Poppins', sans-serif !important;
        }

        .blur {
            filter: blur(4px);
            transition: 0.2s ease-in-out filter;
        }

        .blur:hover {
            filter: blur(0px);
        }
    </style>
</head>
<body>
    @if(auth()->check())
        @include('Layout.header')
    @else
        @include('Layout.starter')
    @endif

    <main>
        <div class="container p-3 py-4 mb-3" id="content">
            <div class="row justify-content-center">
                @yield('content')
            </div>
        </div>
    </main>

    @include('Layout.footer')
    
    <form action="{{ route('logout') }}" method="POST" id="logoutForm">
        @csrf
    </form>                       

    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.0/sweetalert2.all.min.js" integrity="sha512-0UUEaq/z58JSHpPgPv8bvdhHFRswZzxJUT9y+Kld5janc9EWgGEVGfWV1hXvIvAJ8MmsR5d4XV9lsuA90xXqUQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.addEventListener('contextmenu', event => event.preventDefault());

        $(".after-card").hide();
        $(document).ready(function () {
            $(".after-card").fadeIn("slow");
            $("input").change(function (e) {
                e.preventDefault();
                $(".form-text, .alert-danger, .form-group .text-danger").hide();
            });

            @if($errors->any())
                @foreach($errors->all() as $error)
                    showPopup('Error', {!! json_encode($error) !!});
                @endforeach
            @endif

            @if(session()->has('msgSuccess'))
                showPopup('Success', @json(session('msgSuccess')));
            @endif

            @if(session()->has('msgWarning'))
                showPopup('Warning', @json(session('msgWarning')));
            @endif

            @if(session()->has('msgInfo'))
                showPopup('Info', @json(session('msgInfo')));
            @endif
        });

        function showMessage(type, message) {
            return Swal.fire({
                title: type,
                html: message,
                icon: type.toLowerCase(),
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        };

        function showPopup(type, message) {
            Toast.fire({
                html: message,
                icon: type.toLowerCase(),
            });
        }

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener("mouseenter", Swal.stopTimer);
                toast.addEventListener("mouseleave", Swal.resumeTimer);
            },
        });

        document.getElementById('logoutBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to logout",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        });
    </script>
</body>
</html>