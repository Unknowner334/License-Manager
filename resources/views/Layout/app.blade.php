<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    @auth
        <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.tailwindcss.css">
        <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.3.6/js/dataTables.tailwindcss.js"></script>
    @endauth
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.0/sweetalert2.all.min.js" integrity="sha512-0UUEaq/z58JSHpPgPv8bvdhHFRswZzxJUT9y+Kld5janc9EWgGEVGfWV1hXvIvAJ8MmsR5d4XV9lsuA90xXqUQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body class="font-sans bg-light overflow-y-hidden"
      @auth 
      x-data="{ 
          activePage: sessionStorage.getItem('activePage') || 'home',
          sidebarOpen: false
      }" 
      x-init="$watch('activePage', value => {
        sessionStorage.setItem('activePage', value)
        LoadTable(sessionStorage.getItem('activePage', value));
        })"
      @endauth>
    <script>
        window.APP = {
            errors: @json($errors->all()),
            success: @json(session('msgSuccess')),
            warning: @json(session('msgWarning')),
            info: @json(session('msgInfo')),
        };
    </script>
    @vite('resources/js/app.js')

    @include('Layout.header')
    
    <div class="flex min-h-screen">
        @yield('content')
    </div>

    @include('Layout.footer')

    <form action="{{ route('logout') }}" method="post" id="logoutForm"></form>
</body>
</html>