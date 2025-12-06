@extends('Layout.app')

@section('title', 'Licenses')

@php
    use App\Http\Controllers\LicenseController;
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-10">
        @include('Layout.msgStatus')
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                Licenses Registered
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-light btn-sm ms-1" id="reloadBtn"><i class="bi bi-arrow-clockwise"></i> REFRESH</button>
                    <a href="{{ route('licenses.generate') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-key"></i> LICENSE</a>
                    <button class="btn btn-secondary btn-sm ms-1" id="blur-out" data-bs-toggle="tooltip" data-bs-placement="top" title="Eye Protect"><i class="bi bi-eye-slash"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-hover text-center dataTable no-footer" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Owner</th>
                                <th>App</th>
                                <th>User Licenses</th>
                                <th>Devices</th>
                                <th>Duration</th>
                                <th>Created</th>
                                <th>Registrar</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        $(document).ready(function() {
            const table = $('#datatable').DataTable({
                processing: true,
                responsive: true,
                pageLength: 10,
                lengthChange: true,
                ordering: true,
                order: [[0,'desc']],
                ajax: {
                    url: '{{ route('licenses.data') }}',
                    type: 'GET',
                    dataSrc: 'data'
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
                        render: function(data, type, row) {
                            let url = `{{ route('licenses.edit') }}/${data}`;
                            return `
                            <button type="button" class="btn btn-outline-danger btn-sm resetApiKey" data-id="${data}"><i class="bi bi-bootstrap-reboot"></i></button>
                            <a href='${url}' class="btn btn-outline-dark btn-sm"><i class="bi bi-pencil-square"></i></a>
                            `;
                        }
                    }
                ],
                columnDefs: [
                    { targets: [4], searchable: false },
                    { targets: [0, 3, 5, 6, 8], searchable: true },
                    { targets: [1, 2, 7], visible: false, searchable: true },
                    { orderable: false, targets: -1 }
                ]
            });

            $('#reloadBtn').on('click', function () {
                table.ajax.reload(null, false);
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

            $(document).on('click', '.resetApiKey', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to reset the license?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, reset'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Toast.fire({
                            icon: 'info',
                            title: 'Please wait...'
                        })

                        const url = "{{ route('licenses.resetApiKey') }}"

                        $.ajax({
                            url: `${url}/${id}`,
                            type: "GET",
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

                        table.ajax.reload(null, false);
                    }
                });
            });

            $(document).on('click', '.copy-trigger', async function() {
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
    </script>
@endsection