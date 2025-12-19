@extends('Layout.app')

@section('title', 'Login')

@section('content')
    <main class="flex-1 flex flex-col items-center justify-center gap-4">
        <div class="w-full max-w-xs bg-dark rounded-md shadow-lg p-5">
            <h1 class="text-2xl font-semibold text-gray-300 mb-6 text-center">
                Login
            </h1>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div class="mb-4">
                    <label class="block text-md text-gray-300 mb-1">Username</label>
                    <input 
                        type="username" 
                        id="username" 
                        name="username" 
                        class="w-full px-4 py-2 pr-10 border border-dark-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-gray-300 bg-dark-4"
                        placeholder="Enter your username"
                    />
                </div>

                <div class="mb-4">
                    <label class="block text-md text-gray-300 mb-1">Password</label>
                    <div class="relative w-full">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="w-full px-4 py-2 pr-10 border border-dark-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-gray-300 bg-dark-4"
                            placeholder="Enter your password"
                        />

                        <button 
                            type="button" 
                            id="togglePassword"
                            class="absolute inset-y-0 right-2 flex items-center text-gray-400 hover:text-gray-200"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4 flex items-center">
                    <input
                        id="stay_log"
                        name="stay_log"
                        type="checkbox"
                        value="1"
                        class="
                            w-4 h-4 appearance-none rounded border border-dark-3 bg-dark-4 cursor-pointer checked:bg-primary checked:before:w-full
                            checked:before:h-full checked:border-primary focus:outline-none focus:ring-2 focus:ring-primary/40 focus:ring-offset-2
                            focus:ring-offset-dark checked:before:content-['✔'] checked:before:text-white checked:before:text-xs 
                            checked:before:flex checked:before:items-center checked:before:justify-center before:pointer-events-none
                            "
                    />
                    <label for="stay_log" class="ml-2 text-sm text-gray-300 cursor-pointer">
                        Remember me?
                    </label>
                </div>

                <div class="mb-4">
                    <button type="submit" 
                        class="w-auto p-3 bg-transparent border border-dark-3 hover:bg-dark-3
                            text-gray-300 rounded-lg transition-colors duration-100">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-gray-300">
                        Forgot your password? <a href="" class="text-white underline">Click here</a>.
                    </p>
                </div>
            </form>
        </div>

        <div class="w-full max-w-xs bg-dark rounded-md shadow-lg p-2">
            <div class="text-center">
                <p class="text-gray-300 text-[14px]">
                    Don't have an account yet? <a href="{{ route('register') }}" class="text-white underline">Register here</a>.
                </p>
            </div>
        </div>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const eyeIcon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        });
    </script>
@endsection