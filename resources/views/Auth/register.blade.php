@extends('Layout.app')

@section('title', 'Register')

@section('content')
    <main class="flex-1 flex flex-col items-center mt-15 gap-4">
        <div>
            <div class="w-full max-w-xs bg-dark rounded-t shadow px-5 py-2 text-center">
                <h1 class="text-lg text-white">
                    Register
                </h1>
            </div>
            <div class="w-full max-w-xs bg-white rounded-b shadow p-5">
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
                        <label class="block text-md text-dark-text mb-1">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="w-full px-4 py-2 pr-10 border border-dark-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-dark-text"
                            placeholder="Enter your email"
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

                    <div class="mb-4">
                        <label class="block text-md text-dark-text mb-1">Confirm Password</label>
                        <div class="relative w-full">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                class="w-full px-4 py-2 pr-10 border border-dark-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-dark-text"
                                placeholder="Enter your password again"
                            />

                            <button 
                                type="button" 
                                id="togglePasswordConfirmation"
                                class="absolute inset-y-0 right-2 flex items-center text-dark-text hover:text-gray-400"
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-md text-dark-text mb-1">Referrable Code</label>
                        <div class="relative w-full">
                            <input 
                                type="password" 
                                id="reff" 
                                name="reff" 
                                class="w-full px-4 py-2 pr-10 border border-dark-border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-dark-text"
                                placeholder="Enter your referrable code"
                            />

                            <button 
                                type="button" 
                                id="toggleReff"
                                class="absolute inset-y-0 right-2 flex items-center text-dark-text hover:text-gray-400"
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                            class="w-auto p-3 bg-transparent border border-dark-border hover:bg-primary hover:border-transparent
                                text-dark-text hover:text-white rounded-lg transition-colors duration-150">
                            <i class="bi bi-person-plus"></i> Register
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="w-full max-w-xs bg-white rounded shadow p-2">
                <div class="text-center">
                    <p class="text-dark-text text-[15px]">
                        Already have an account? <a href="{{ route('login') }}" class="text-primary underline">Login here</a>.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        const reffInput = document.getElementById('reff');
        const togglePassword = document.getElementById('togglePassword');
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirmation');
        const toggleReff = document.getElementById('toggleReff');
        const eyeIcon = togglePassword.querySelector('i');
        const eyeIcon2 = togglePasswordConfirm.querySelector('i');
        const eyeIcon3 = toggleReff.querySelector('i');

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

        togglePasswordConfirm.addEventListener('click', () => {
            if (passwordConfirmInput.type === 'password') {
                passwordConfirmInput.type = 'text';
                eyeIcon2.classList.remove('bi-eye');
                eyeIcon2.classList.add('bi-eye-slash');
            } else {
                passwordConfirmInput.type = 'password';
                eyeIcon2.classList.remove('bi-eye-slash');
                eyeIcon2.classList.add('bi-eye');
            }
        });

        toggleReff.addEventListener('click', () => {
            if (reffInput.type === 'password') {
                reffInput.type = 'text';
                eyeIcon3.classList.remove('bi-eye');
                eyeIcon3.classList.add('bi-eye-slash');
            } else {
                reffInput.type = 'password';
                eyeIcon3.classList.remove('bi-eye-slash');
                eyeIcon3.classList.add('bi-eye');
            }
        });
    </script>
@endsection