let apps_table = null;

window.initAppsTable = function () {
    if (apps_table) return;

    apps_table = $('#apps_table').DataTable({
        processing: true,
        responsive: true,
        deferLoading: 0,
        order: [[0,'desc']],
        ajax: {
            url: window.APP.routes.appRegistrations,
            method: "POST",
        },
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
                    return `
                    <button type="button" class="px-2 py-1 border border-dark rounded hover:bg-dark hover:text-white transition-colors duration-200 cursor-pointer copy-app" data-copy="${data[1]}" data-name="${data[2]}"><i class="bi bi-clipboard"></i></button>
                    <button type="button" class="px-2 py-1 border border-dark rounded hover:bg-dark hover:text-white transition-colors duration-200 cursor-pointer" id="editBtnApps" data-app="${data[0]}"><i class="bi bi-pencil-square"></i></button>
                    <button type="button" class="px-2 py-1 border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white transition-colors duration-200 cursor-pointer" id="deleteBtnApps" data-app="${data[0]}" data-name="${data[2]}"><i class="bi bi-trash"></i></button>
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

window.AppsTableReload = function () {
    if (apps_table) {
        apps_table.ajax.reload(null, false);
    }
}

window.createApp = function () {
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
        showCancelButton: true,
        cancelButtonText: 'Cancel',
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
        Toast.fire({
            icon: 'info',
            html: 'Processing...',
        });

        $.ajax({
            url: window.APP.routes.appRegister,
            method: 'POST',
            data: result.value,
            headers: {
                'X-CSRF-TOKEN': window.APP.csrf
            },
            success: function(res) {
                if (res.status == 0) {
                    window.showPopup('Success', res.message);
                    AppsTableReload();
                } else {
                    window.showPopup('Error', res.message);
                }
            },
            error: function(err) {
                const message = err.responseJSON?.message || 'Something went wrong';
                window.showPopup('Error', message);
            }
        });
    });
}

window.updateAppForm = function (id, app_id, app_name, app_status, app_price) {
    Swal.fire({
        title: 'Update App',
        html: `
            <input type="hidden" id="editId" value="${id}">
            <input type="text" id="appId" class="swal2-input" placeholder="App ID" value="${app_id}">
            <input type="text" id="appName" class="swal2-input" placeholder="App Name" value="${app_name}">
            <select id="appStatus" class="swal2-input">
                <option value="">-- Select Status --</option>
                <option value="Active" ${app_status === 'Active' ? 'selected' : ''}>Active</option>
                <option value="Inactive" ${app_status === 'Inactive' ? 'selected' : ''}>Inactive</option>
            </select>
            <input type="number" id="appPrice" class="swal2-input" placeholder="Price" value="${app_price}">
        `,
        confirmButtonText: 'Update',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        focusConfirm: false,
        preConfirm: () => {
            const edit_id = document.getElementById('editId').value;
            const app_id = document.getElementById('appId').value.trim();
            const name = document.getElementById('appName').value.trim();
            const status = document.getElementById('appStatus').value;
            const price = document.getElementById('appPrice').value;

            if (!edit_id) {
                Swal.showValidationMessage('Edit ID is required');
                return false;
            }
            if (!app_id) {
                Swal.showValidationMessage('App ID is required');
                return false;
            }
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

            return { edit_id, app_id, name, status, price };
        }
    }).then((result) => {
        if (!result.isConfirmed) return;
        Toast.fire({
            icon: 'info',
            html: 'Processing...',
        });

        $.ajax({
            url: window.APP.routes.appUpdate,
            method: 'POST',
            data: result.value,
            headers: {
                'X-CSRF-TOKEN': window.APP.csrf
            },
            success: function(res) {
                if (res.status == 0) {
                    window.showPopup('Success', res.message);
                    AppsTableReload();
                } else {
                    window.showPopup('Error', res.message);
                }
            },
            error: function(err) {
                const message = err.responseJSON?.message || 'Something went wrong';
                window.showPopup('Error', message);
            }
        });
    });
};

window.updateApp = function (id) {
    $.ajax({
        url: window.APP.routes.appData,
        method: 'POST',
        data: { id: id },
        success: function(res) {
            if (res.status == 0) {
                updateAppForm(id, res.app_id, res.app_name, res.app_status, res.price);
            } else {
                window.showPopup('Error', res.message);
            }
        },
        error: function(err) {
            const message = err.responseJSON?.message || 'Something went wrong';
            window.showPopup('Error', message);
        }
    });
};

window.deleteApp = function (id, name) {
    Swal.fire({
        icon: 'info',
        title: 'Delete App',
        html: `Are you sure you want to delete app <b>${name}</b>?`,
        confirmButtonText: 'Delete',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        focusConfirm: false,
    }).then((result) => {
        if (!result.isConfirmed) return;
        Toast.fire({
            icon: 'info',
            html: 'Processing...',
        });

        $.ajax({
            url: window.APP.routes.appDelete,
            method: 'POST',
            data: { edit_id: id },
            headers: {
                'X-CSRF-TOKEN': window.APP.csrf
            },
            success: function(res) {
                if (res.status == 0) {
                    window.showPopup('Success', res.message);
                    AppsTableReload();
                } else {
                    window.showPopup('Error', res.message);
                }
            },
            error: function(err) {
                const message = err.responseJSON?.message || 'Something went wrong';
                window.showPopup('Error', message);
            }
        });
    });
};

$(document).ready(function () {
    $('#reloadBtnApps').on('click', () => {
        AppsTableReload();
    });

    $('#createBtnApps').on('click', () => {
        createApp();
    });

    $(document).on('click', '#editBtnApps', async function() {
        const id = $(this).data('app');
        updateApp(id);
    });

    $(document).on('click', '#deleteBtnApps', async function() {
        const id = $(this).data('app');
        const name = $(this).data('name');
        deleteApp(id, name);
    });

    $(document).on('click', '.copy-app', async function() {
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