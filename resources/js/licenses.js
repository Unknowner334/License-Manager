let licenses_table = null;

window.initLicensesTable = function () {
    if (licenses_table) return;

    licenses_table = $('#licenses_table').DataTable({
        processing: true,
        responsive: true,
        deferLoading: 0,
        order: [[0,'desc']],
        ajax: {
            url: window.APP.routes.licenseRegistrations,
            method: "POST",
        },
        columns: [
            { data: 'id' },
            { data: 'owner' },
            { data: 'app' },
            { data: 'user_key' },
            { data: 'devices' },
            { data: 'duration' },
            { data: 'created' },
            { data: 'registrar' },
            { data: 'price' },
            {
                data: 'edit_id',
                render: function(data) {
                    return `
                    <button type="button" class="px-2 py-1 border border-red-600 text-red-600 rounded hover:bg-red-600 hover:text-white transition-colors duration-200 cursor-pointer resetApiKey" data-id="${data}"><i class="bi bi-bootstrap-reboot"></i></button>
                    <button type="button" class="px-2 py-1 border border-dark rounded hover:bg-dark hover:text-white transition-colors duration-200 cursor-pointer" id="updateBtnLicenses" data-id="${data}"><i class="bi bi-pencil-square"></i></button>
                    `;
                }
            }
        ],
        columnDefs: [
            { targets: [4], searchable: false },
            { targets: [0, 3, 5, 6, 8], searchable: true },
            { targets: [1, 2, 7], visible: false, searchable: true },
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

window.LicensesTableReload = function () {
    if (licenses_table) {
        licenses_table.ajax.reload(null, false);
    }
}

window.createLicense = function () {
    Swal.fire({
        title: 'Create License',
        html: `
            <select id="app" class="swal2-input appSelect"></select>
            <input type="text" id="owner" class="swal2-input" placeholder="Owner">
            <select id="status" class="swal2-input">
                <option value="">-- Select Status --</option>
                <option value="Active" selected>Active</option>
                <option value="Inactive">Inactive</option>
            </select>
            <select id="duration" class="swal2-input durationSelect"></select>
            <input type="number" id="devices" class="swal2-input" placeholder="Max Devices" value='1'>
        `,
        confirmButtonText: 'Create',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        focusConfirm: false,
        didOpen: () => {
            loadAppList();
            loadDurationList();
        },
        preConfirm: () => {
            const app = document.getElementById('app').value.trim();
            const owner = document.getElementById('owner').value.trim();
            const status = document.getElementById('status').value;
            const duration = document.getElementById('duration').value;
            const devices = document.getElementById('devices').value;

            if (!app) {
                Swal.showValidationMessage('App is required');
                return false;
            }
            if (!status) {
                Swal.showValidationMessage('Status must be selected');
                return false;
            }
            if (!duration) {
                Swal.showValidationMessage('Duration must be selected');
                return false;
            }
            if (!devices) {
                Swal.showValidationMessage('Devices are required');
                return false;
            }

            return { app, owner, status, duration, devices };
        }
    }).then((result) => {
        if (!result.isConfirmed) return;
        Toast.fire({
            icon: 'info',
            html: 'Processing...',
        });

        $.ajax({
            url: window.APP.routes.licenseRegister,
            method: 'POST',
            data: result.value,
            headers: {
                'X-CSRF-TOKEN': window.APP.csrf
            },
            success: function(res) {
                if (res.status == 0) {
                    window.showPopup('Success', res.message);
                    LicensesTableReload();
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

window.updateLicenseForm = function (id, owner, app, license, status, duration, devices) {
    Swal.fire({
        title: 'Update License',
        html: `
        <input type="hidden" id="editId" value="${id}">
            <select id="app" class="swal2-input appSelect" data-filled="${app}"></select>
            <input type="text" id="license" class="swal2-input" placeholder="License (leave empty for random)" value="${license}">
            <input type="text" id="owner" class="swal2-input" placeholder="Owner" value="${owner}">
            <select id="status" class="swal2-input">
                <option value="">-- Select Status --</option>
                <option value="Active" ${status === 'Active' ? 'selected' : ''}>Active</option>
                <option value="Inactive" ${status === 'Active' ? 'selected' : ''}>Inactive</option>
            </select>
            <select id="duration" class="swal2-input durationSelect" data-filled="${duration}"></select>
            <input type="number" id="devices" class="swal2-input" placeholder="Max Devices" value='${devices}'>
        `,
        confirmButtonText: 'Update',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        focusConfirm: false,
        didOpen: () => {
            loadAppList();
            loadDurationList();
        },
        preConfirm: () => {
            const edit_id = document.getElementById('editId').value;
            const app = document.getElementById('app').value;
            const license = document.getElementById('license').value.trim();
            const owner = document.getElementById('owner').value.trim();
            const status = document.getElementById('status').value;
            const duration = document.getElementById('duration').value;
            const devices = document.getElementById('devices').value;

            if (!edit_id) {
                Swal.showValidationMessage('Edit ID is required');
                return false;
            }
            if (!app) {
                Swal.showValidationMessage('App is required');
                return false;
            }
            if (!status) {
                Swal.showValidationMessage('Status must be selected');
                return false;
            }
            if (!duration) {
                Swal.showValidationMessage('Duration must be selected');
                return false;
            }
            if (!devices) {
                Swal.showValidationMessage('Devices are required');
                return false;
            }

            return { edit_id, app, license, owner, status, duration, devices };
        }
    }).then((result) => {
        if (!result.isConfirmed) return;
        Toast.fire({
            icon: 'info',
            html: 'Processing...',
        });

        $.ajax({
            url: window.APP.routes.licenseUpdate,
            method: 'POST',
            data: result.value,
            headers: {
                'X-CSRF-TOKEN': window.APP.csrf
            },
            success: function(res) {
                if (res.status == 0) {
                    window.showPopup('Success', res.message);
                    LicensesTableReload();
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

window.updateLicense = function (id) {
    $.ajax({
        url: window.APP.routes.licenseData,
        method: 'POST',
        data: { id: id },
        success: function(res) {
            if (res.status == 0) {
                updateLicenseForm(id, res.owner, res.app, res.user_license, res.license_status, res.duration, res.max_devices);
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

$(document).ready(function () {
    $('#reloadBtnLicenses').on('click', function () {
        LicensesTableReload();
    });
    
    $('#createBtnLicenses').on('click', () => {
        createLicense();
    });

    $(document).on('click', '#updateBtnLicenses', async function() {
        const id = $(this).data('id');
        updateLicense(id);
    });

    $("#blur-out").click(function() {
        if ($(".Blur").hasClass("blur")) {
            $(".Blur").removeClass("blur");
            $("#blur-out").html(`<i class="bi bi-eye"></i>`);
        } else {
            $(".Blur").addClass("blur");
            $("#blur-out").html(`<i class="bi bi-eye-slash"></i>`);
        }
    });

    $(document).on('click', '.copy-license', async function() {
        const copy = $(this).data('copy');

        const code = await copyToClipboard(copy);

        let message = "";
        let type = "error";

        switch (code) {
            case 0:
                message = `<b>License</b> ${copy} <b>Successfully Copied</b>`;
                type = "success";
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
            icon: type,
        });
    });
});