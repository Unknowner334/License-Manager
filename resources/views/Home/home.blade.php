<section class="flex flex-col lg:flex-row gap-4 w-full items-stretch">
    <div class="flex flex-col min-w-0 lg:flex-3">
        @php
            $homeLicenseTitle="
            <div class='flex justify-between items-center'>
                Registrations History

                <button id='reloadBtnDashboard'
                        class='bg-transparent text-white border border-white hover:border-transparent hover:bg-primary uppercase px-2 py-1 
                        rounded shadow transition duration-200 flex items-center gap-2 text-[14px]'>
                    <i class='bi bi-arrow-clockwise'></i>
                    Refresh
                </button>
            </div>
            ";
        @endphp
        <x-card title="{!! $homeLicenseTitle !!}">
            <div class="overflow-auto relative scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200">
                <table class="w-full min-w-full divide-y divide-gray-200" id="licenses_table">
                    <thead class="bg-gray-50">
                        <tr class="border border-gray-200">
                            <th class="px-6 py-3 text-left text-xs font-medium text-dark-text uppercase tracking-wider">
                                #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-dark-text uppercase tracking-wider">
                                User License
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-dark-text uppercase tracking-wider">
                                Duration
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-dark-text uppercase tracking-wider">
                                Registrar
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-dark-text uppercase tracking-wider">
                                Devices
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-dark-text uppercase tracking-wider">
                                Created
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </x-card>
    </div>

    <div class="flex flex-col min-w-0 lg:flex-2 xl:flex-1">
        <x-card title="Information" class="w-full max-w-xs">
            <ul class="mb-2">
                <li class="bg-white hover:bg-gray-200 rounded-t px-5 py-2 flex justify-between align-middle border border-gray-200">
                    Name
                    <span class="text-sm text-dark">{{ auth()->user()->name }}</span>
                </li>
                <li class="bg-white hover:bg-gray-200 px-5 py-2 flex justify-between align-middle border border-gray-200">
                    Roles
                    <span class="text-sm text-{{ permissionColor(auth()->user()->role) }}">{{ auth()->user()->role }}</span>
                </li>
                <li class="bg-white hover:bg-gray-200 rounded-b px-5 py-2 flex justify-between align-middle border border-gray-200">
                    Saldo
                    @php $saldo = saldoData(auth()->user()->saldo, auth()->user()->role); @endphp
                    <span class="text-sm text-{{ $saldo[1] }}">{{ $saldo[0] }}</span>
                </li>
            </ul>
            <ul class="mb-2">
                <li class="bg-white hover:bg-gray-200 rounded-t px-5 py-2 flex justify-between align-middle border border-gray-200">
                    Login Time
                    <span id="login-timer" class="text-sm text-dark-text" data-logintime="{{ $loginTime ? $loginTime->toIso8601String() : null }}"></span>
                </li>
                <li class="bg-white hover:bg-gray-200 rounded-b px-5 py-2 flex justify-between align-middle border border-gray-200">
                    Auto Logout
                    <span id="expiry-timer" class="text-sm text-dark-text" data-expiry="{{ $expiryTime }}"></span>
                </li>
            </ul>
        </x-card>
    </div>
</section>

<script>
    let dashboard_table = null;

    function initDashboardTable() {
        if (dashboard_table) return;

        dashboard_table = $('#licenses_table').DataTable({
            processing: true,
            responsive: true,
            paging: false,
            info: false,
            searching: false,
            lengthChange: false,
            deferLoading: 0,
            order: [[0,'desc']],
            ajax: {
                url: "{{ route('api.private.home.registrations') }}",
                method: "POST",
            },
            columns: [
                { data: 'id' },
                { data: 'user_key' },
                { data: 'duration' },
                { data: 'registrar' },
                { data: 'devices' },
                { data: 'created' },
            ],
            scrollX: true,
            stripeClasses: [],
            createdRow: function (row, data) {
                $(row).find('td').removeClass('p-3').addClass('px-6 py-3 text-center text-xs font-semibold text-dark-text border border-gray-200');
            },
            language: {
                processing: `
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2
                                bg-primary text-white px-4 py-2 rounded shadow z-50">
                        Processing...
                    </div>
                `
            }
        });
    }

    function DashboardTableReload() {
        if (dashboard_table) {
            dashboard_table.ajax.reload(null, false);
        }
    }

    $(document).ready(function () {
        $('#reloadBtnDashboard').on('click', () => {
            DashboardTableReload();
        });
    });

    function updateLoginTime() {
        const timerElem = document.getElementById('login-timer');
        const loginTimeStr = timerElem.getAttribute('data-logintime');

        if (!loginTimeStr) {
            timerElem.textContent = 'never logged in';
            return 60000;
        }

        const loginTime = new Date(loginTimeStr).getTime();
        if (isNaN(loginTime)) {
            timerElem.textContent = 'invalid date';
            return 60000;
        }

        const now = Date.now();
        const diff = Math.floor((now - loginTime) / 1000);

        let display = '';
        if (diff < 60) {
            display = diff + ' seconds ago';
        } else if (diff < 3600) {
            const minutes = Math.floor(diff / 60);
            display = `${minutes} minutes ago`;
        } else if (diff < 86400) {
            const hours = Math.floor(diff / 3600);
            display = `${hours} hours ago`;
        } else {
            const days = Math.floor(diff / 86400);
            display = `${days} days ago`;
        }

        timerElem.textContent = display;

        if (diff < 60) return 1000;
        else if (diff < 3600) return 30000;
        else return 300000;
    }

    function updateExpiryTime() {
        const expiryElem = document.getElementById('expiry-timer');
        const expiryStr = expiryElem.getAttribute('data-expiry');

        if (!expiryStr) {
            expiryElem.textContent = 'no expiry';
            return 60000;
        }

        const expiryTime = new Date(expiryStr).getTime();
        if (isNaN(expiryTime)) {
            expiryElem.textContent = 'invalid expiry';
            return 60000;
        }

        const now = Date.now();
        let diff = Math.floor((expiryTime - now) / 1000);

        if (diff <= 0) {
            expiryElem.textContent = 'expired';
            return 60000;
        }

        let display = '';
        if (diff < 60) {
            display = `in ${diff} seconds`;
        } else if (diff < 3600) {
            const minutes = Math.floor(diff / 60);
            display = `in ${minutes} minutes`;
        } else if (diff < 86400) {
            const hours = Math.floor(diff / 3600);
            display = `in ${hours} hours`;
        } else {
            const days = Math.floor(diff / 86400);
            display = `in ${days} days`;
        }

        expiryElem.textContent = display;

        if (diff < 60) return 1000;
        else if (diff < 3600) return 30000;
        else return 300000;
    }

    function startExpiryTimer() {
        const interval = updateExpiryTime();
        setTimeout(startExpiryTimer, interval);
    }

    function startLoginTimer() {
        const interval = updateLoginTime();
        setTimeout(startLoginTimer, interval);
    }

    startExpiryTimer();
    startLoginTimer();
</script>