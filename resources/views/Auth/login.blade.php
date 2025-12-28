@extends('Layout.app')

@section('title', 'Login')

@section('content')
    <main class="flex-1 flex flex-col items-center mt-15 gap-4" 
            x-data="{ forgot: JSON.parse(sessionStorage.getItem('forgotYourPassword') || 'false') }"
            x-init="$watch('forgot', value => sessionStorage.setItem('forgotYourPassword', value))">
        <div x-show="!forgot" x-cloak>
            <x-card title="Login" class="w-full max-w-xs">
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf
                    @honeypot
                    <div class="mb-4">
                        <label class="block text-md text-dark-text mb-1">Username</label>
                        <input 
                            type="username" 
                            id="username" 
                            name="username" 
                            class="w-full px-4 py-2 pr-10 border border-dark-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-dark-text"
                            placeholder="Enter your username"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="block text-md text-dark-text mb-1">Password</label>
                        <div class="relative w-full">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="w-full px-4 py-2 pr-10 border border-dark-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-dark-text"
                                placeholder="Enter your password"
                            />

                            <button 
                                type="button" 
                                id="togglePassword"
                                class="absolute inset-y-0 right-2 flex items-center text-dark-text hover:text-gray-400"
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
                                w-4 h-4 appearance-none rounded border border-dark-border bg-white cursor-pointer checked:bg-primary checked:before:w-full
                                checked:before:h-full checked:border-primary focus:outline-none focus:ring-2 focus:ring-primary/40 focus:ring-offset-2
                                focus:ring-offset-light checked:before:content-['✔'] checked:before:text-white checked:before:text-xs 
                                checked:before:flex checked:before:items-center checked:before:justify-center before:pointer-events-none
                                "
                        />
                        <label for="stay_log" class="ml-2 text-md text-dark-text cursor-pointer">
                            Remember me?
                        </label>
                    </div>

                    <div class="mb-4">
                        <button type="submit" 
                            class="w-auto p-3 bg-transparent border border-dark-border hover:bg-primary hover:border-transparent
                                text-dark-text hover:text-white rounded-lg transition-colors duration-150">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-dark-text">
                            Forgot your password? 
                            <button type="button" class="text-primary underline cursor-pointer" @click="forgot = !forgot">Click here</button>.
                        </p>
                    </div>
                </form>
            </x-card>
        </div>

        <div x-show="!forgot" x-cloak>
            <div class="w-full max-w-xs bg-white rounded shadow-md p-2">
                <div class="text-center">
                    <p class="text-dark-text text-[14px]">
                        Don't have an account yet? <a href="{{ route('register') }}" class="text-primary underline">Register here</a>.
                    </p>
                </div>
            </div>
        </div>

        <div x-show="forgot" x-cloak>
            <x-card title="Forgot Your Password?" class="w-full max-w-xs">
                <form action="{{ route('login.forgot') }}" method="POST" class="space-y-4" id="forgotPasswordForm">
                    @csrf
                    @honeypot
                    <div class="mb-4">
                        <label class="block text-md text-dark-text mb-1">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="w-full px-4 py-2 pr-10 border border-dark-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-dark-text"
                            placeholder="Enter your email"
                        />
                    </div>

                    <button type="button" 
                        class="w-auto p-3 bg-transparent border border-dark-border hover:bg-primary hover:border-transparent
                            text-dark-text hover:text-white rounded-lg transition-colors duration-150" id="forgotPasswordBtn">
                        <i class="bi bi-check2-circle"></i> Submit
                    </button>
                </form>
            </x-card>
        </div>

        <div x-show="forgot" x-cloak>
            <div class="w-full max-w-xs bg-white rounded shadow px-6 py-2">
                <div class="text-center">
                    <p class="text-dark-text text-[16px]">
                        Going back to login? 
                        <button type="button" class="text-primary underline cursor-pointer" @click="forgot = !forgot">Click here</button>.
                    </p>
                </div>
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

        $(document).on('click', '#forgotPasswordBtn', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to send a password reset request?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send'
            }).then((result) => {
                if (result.isConfirmed) {
                    Toast.fire({
                        icon: 'info',
                        title: 'Please wait...'
                    });

                    $('#forgotPasswordForm').trigger('submit');
                }
            });
        });

        $('#forgotPasswordForm').on('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            $.ajax({
                url: "{{ route('login.forgot') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == 0) {
                        showPopup('Success', response.message);
                    } else {
                        showPopup('Error', response.message);
                    }
                },
                error: function (xhr) {
                    showPopup('Error', xhr.responseJSON.message);
                }
            });
        });
    </script>
@endsection