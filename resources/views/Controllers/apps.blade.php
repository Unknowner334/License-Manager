<section class="flex flex-col lg:flex-row gap-4 w-full items-stretch lg:justify-center lg:items-center">
    <div class="flex flex-col min-w-0 lg:w-[95%]">
        <div class="bg-dark rounded-t shadow px-5 py-2 flex justify-between items-center">
            <h1 class="text-md text-white mb-0">
                Apps Registered
            </h1>
            <div class="flex gap-2">
                <button id="reloadBtnApps" 
                        class="bg-transparent text-white border border-white hover:border-transparent hover:bg-primary uppercase px-2 py-1 
                        rounded shadow transition duration-200 flex items-center gap-2">
                    <i class="bi bi-arrow-clockwise"></i>
                    Refresh
                </button>
                <button id="createBtnApps" 
                        class="bg-transparent text-white border border-white hover:border-transparent hover:bg-primary uppercase px-2 py-1 
                        rounded shadow transition duration-200 flex items-center gap-2">
                    <i class="bi bi-terminal"></i>
                    App
                </button>
            </div>
        </div>

        <div class="overflow-auto relative scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200 bg-white rounded-b shadow p-5">
            <table class="w-full min-w-full divide-y divide-gray-200" id="apps_table">
                <thead class="bg-gray-50">
                    <tr class="border border-gray-200">
                        <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                            #
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                            Price
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                            Licenses
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                            Created
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                            Registrar
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-dark-text uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

<script>
    let apps_table = null;

    async function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            try {
                await navigator.clipboard.writeText(text);
                return 0;
            } catch (e) {
                return 1;
            }
        }

        let exitCode = 3;

        const temp = document.createElement("textarea");
        temp.value = text;
        document.body.appendChild(temp);
        temp.select();

        try {
            if (document.execCommand("copy")) {
                exitCode = 0;
            } else {
                exitCode = 2;
            }
        } catch (e) {
            exitCode = 2;
        }

        document.body.removeChild(temp);
        return exitCode;
    }

    function initAppsTable() {
        if (apps_table) return;

        apps_table = $('#apps_table').DataTable({
            processing: true,
            responsive: true,
            order: [[0,'desc']],
            ajax: "{{ route('api.private.apps.registrations') }}",
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'price' },
                { data: 'licenses' },
                { data: 'registrar' },
                { data: 'created' },
                {
                    data: 'ids',
                    render: function(data, type, row) {
                        let url = `#`;
                        return `
                        <button type="button" class="px-2 py-1 border border-dark rounded hover:bg-dark hover:text-white transition-colors duration-200 cursor-pointer copy-trigger" data-copy="${data[1]}" data-name="${data[2]}"><i class="bi bi-clipboard"></i></button>
                        <a href='${url}' class="px-2 py-1 border border-dark rounded hover:bg-dark hover:text-white transition-colors duration-200"><i class="bi bi-pencil-square"></i></a>
                        `;
                    }
                },
            ],
            columnDefs: [
                { targets: [4], searchable: false },
                { targets: [0, 1, 2, 3], searchable: true },
                { targets: [5], visible: false, searchable: true },
                { orderable: false, targets: -1 }
            ],
            scrollX: true,
            stripeClasses: [],
            createdRow: function (row, data, dataIndex) {
                $(row).find('td').removeClass('p-3').addClass('px-6 py-3 text-center text-sm font-semibold text-dark-text border border-gray-200');
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

    function AppsTableReload() {
        if (apps_table) {
            apps_table.ajax.reload(null, false);
        }
    }

    function createApp() {
        Swal.fire({
            title: 'Create App',
            html: `
                <input type="text" id="appName" class="swal2-input" placeholder="App Name">
                <select id="appStatus" class="swal2-input">
                    <option value="">-- Select Status --</option>
                    <option value="Active" selected>Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
                <input type="number" id="appPrice" class="swal2-input" placeholder="Price">
            `,
            confirmButtonText: 'Create',
            focusConfirm: false,
            preConfirm: () => {
                const name = document.getElementById('appName').value.trim();
                const status = document.getElementById('appStatus').value;
                const price = document.getElementById('appPrice').value;

                if (!name) {
                    Swal.showValidationMessage('App Name is required');
                    return false;
                }
                if (!status) {
                    Swal.showValidationMessage('Status must be selected');
                    return false;
                }
                if (!price) {
                    Swal.showValidationMessage('Price is required');
                    return false;
                }

                return { name, status, price };
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: "{{ route('api.private.apps.register') }}",
                method: 'POST',
                data: result.value,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                success: function(res) {
                    window.showPopup('Success', 'App created successfully!');
                    AppsTableReload();
                },
                error: function(err) {
                    const message = err.responseJSON?.message || 'Something went wrong';
                    window.showPopup('Error', message);
                }
            });
        });
    }

    $(document).ready(function () {
        $('#reloadBtnApps').on('click', () => {
            AppsTableReload();
        });

        $('#createBtnApps').on('click', () => {
            createApp();
        });

        $(document).on('click', '.copy-trigger', async function() {
            const copy = $(this).data('copy');
            const name = $(this).data('name');

            const code = await copyToClipboard(copy);

            let message = "";
            let icon = "error";

            switch (code) {
                case 0:
                    message = `<b>App</b> ${name} <b>App's ID Successfully Copied</b>`;
                    icon = "success";
                    break;
                case 1:
                    message = "Clipboard API failed.";
                    break;
                case 2:
                    message = "Fallback copy failed.";
                    break;
                case 3:
                    message = "Clipboard API not available (HTTP or insecure context).";
                    break;
            }

            Toast.fire({
                html: message,
                icon: icon,
            });
        });
    });
</script>