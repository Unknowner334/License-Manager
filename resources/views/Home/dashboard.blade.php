@extends('Layout.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex min-h-screen">
        <aside class="w-64 bg-white shadow-md p-4 hidden md:flex flex-col">
            @include('Layout.sidebar')
        </aside>

        <div x-show="sidebarOpen" 
            x-transition:enter="transition transform ease-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition transform ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            x-cloak
            class="fixed inset-0 z-50 flex md:hidden">
            <aside class="relative bg-white h-full w-full shadow-md p-4 flex flex-col">
                <button @click="sidebarOpen = false" class="mb-4 p-2 rounded hover:bg-gray-100 self-end">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>

                @include('Layout.sidebar')
            </aside>
        </div>

        <main class="flex-1 p-6">
            <div x-show="activePage==='home'" x-cloak>
                @include('Home.home')
            </div>

            <div x-show="activePage==='apps'" x-cloak>
                apps
            </div>

            <div x-show="activePage==='licenses'" x-cloak>
                licenses
            </div>

            @if (auth()->user()->role != "Reseller")
            <div x-show="activePage==='users'" x-cloak>
                users
            </div>

            <div x-show="activePage==='reff'" x-cloak>
                referrables
            </div>

            <div x-show="activePage==='webui_settings'" x-cloak>
                webui
            </div>
            @endif

            <div x-show="activePage==='settings'" x-cloak>
                settings
            </div>
        </main>
    </div>
@endsection