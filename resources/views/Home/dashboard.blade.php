@extends('Layout.app')

@section('title', 'Dashboard')

@section('content')
    <aside class="w-64 bg-white shadow-md p-4 hidden lg:flex flex-col">
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
        class="fixed inset-0 z-50 flex lg:hidden">
        <aside class="relative bg-white h-full w-full shadow-md p-4 flex flex-col">
            <button @click="sidebarOpen = false" class="mb-4 p-2 rounded hover:bg-gray-100 self-end">
                <i class="bi bi-x-lg text-lg"></i>
            </button>

            @include('Layout.sidebar')
        </aside>
    </div>

    <main class="flex-1 flex flex-col items-center mt-15 overflow-y-hidden overflow-x-auto">
        <div class="w-[70%]" x-show="activePage==='home'" x-cloak>
            @include('Home.home')
        </div>

        <div class="w-[70%]" x-show="activePage==='apps'" x-cloak>
            @include('Controllers.apps')
        </div>

        <div x-show="activePage==='licenses'" x-cloak>
            @include('Controllers.licenses')
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

    <script>
        window.APP = {
            routes: {
                homeRegistrations: "{{ route('api.home.registrations') }}",
                appRegistrations: "{{ route('api.apps.registrations') }}",
                appData: "{{ route('api.apps.data') }}",
                appRegister: "{{ route('api.apps.register') }}",
                appUpdate: "{{ route('api.apps.update') }}",
                appDelete: "{{ route('api.apps.delete') }}",
                licenseRegistrations: "{{ route('api.licenses.registrations') }}",
                licenseData: "{{ route('api.licenses.data') }}",
                licenseRegister: "{{ route('api.licenses.register') }}",
                licenseUpdate: "{{ route('api.licenses.update') }}",
                licenseDelete: "{{ route('api.licenses.delete') }}",
            },
            csrf: "{{ csrf_token() }}"
        };

        function loadAppList() {
            $.post(window.APP.routes.appRegistrations, {}, function(res) {
                if (res.status === 0 && res.data.length) {
                    $('.appSelect').each(function() {
                        const $select = $(this);
                        $select.empty();
                        $select.append(`<option value="">-- Select App --</option>`);
                        
                        res.data.forEach((app, index) => {
                            const price = app.price;
                            $select.append(`
                                <option value="${app.app_id}" ${index === 0 ? 'selected' : ''}>
                                    ${app.name} - ${price}
                                </option>
                            `);
                        });
                    });
                }
            });
        }

        function LoadTable(page) {
            if (page === 'home') {
                initDashboardTable();
            }

            if (page === 'apps') {
                initAppsTable();
            }

            if (page === 'licenses') {
                initLicensesTable();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const activePage = sessionStorage.getItem('activePage') || 'home';

            LoadTable(activePage);
        });
    </script>
@endsection