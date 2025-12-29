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
                    <button type="button" class="btn btn-outline-danger btn-sm resetApiKey" data-id="${data}"><i class="bi bi-bootstrap-reboot"></i></button>
                    <a href='#' class="btn btn-outline-dark btn-sm"><i class="bi bi-pencil-square"></i></a>
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

$(document).ready(function () {
    $('#reloadBtnLicenses').on('click', function () {
        LicensesTableReload();
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