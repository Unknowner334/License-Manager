@extends('Layout.app')

@section('title', 'Users')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-10">
        @include('Layout.msgStatus')
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                Users Registered
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-light btn-sm ms-1" id="reloadBtn"><i class="bi bi-arrow-clockwise"></i> REFRESH</button>
                    <a class="btn btn-outline-light btn-sm" href={{ route('admin.users.generate') }}><i class="bi bi-person"></i> USER</a>
                    <button class="btn btn-secondary btn-sm ms-1" id="blur-out" data-bs-toggle="tooltip" data-bs-placement="top" title="Eye Protect"><i class="bi bi-eye-slash"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-hover text-center dataTable no-footer" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Saldo</th>
                                <th>Role</th>
                                <th>Reff</th>
                                <th>Registrar</th>
                                <th>Created</th>
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
                    url: '{{ route('admin.users.data') }}',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'id' },
                    { data: 'name' },
                    { data: 'username' },
                    { data: 'saldo' },
                    { data: 'role' },
                    { data: 'reff' },
                    { data: 'registrar' },
                    { data: 'created' },
                    {
                        data: 'user_id',
                        render: function(data, type, row) {
                            let editUrl = `{{ route('admin.users.edit') }}/${data}`;
                            let walletUrl = `{{ route('admin.users.wallet') }}/${data}`;
                            let historyUrl = `{{ route('admin.users.history.user') }}/${data}`;
                            return `
                            <a href="${walletUrl}" class="btn btn-outline-dark btn-sm">
                                <i class="bi bi-wallet"></i>
                            </a>

                            <a href="${historyUrl}" class="btn btn-outline-dark btn-sm">
                                <i class="bi bi-person"></i>
                            </a>

                            <a href="${editUrl}" class="btn btn-outline-dark btn-sm">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            `;
                        }
                    },
                ],
                columnDefs: [
                    { targets: [7, 8], searchable: false },
                    { targets: [0, 1, 2, 4], searchable: true },
                    { targets: [5, 6], visible: false, searchable: true },
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

            $(document).on('click', '.copy-trigger', async function() {
                const copy = $(this).data('copy');

                const code = await copyToClipboard(copy);

                let message = "";
                let icon = "error";

                switch (code) {
                    case 0:
                        message = `<b>User</b> ${copy} <b>Successfully Copied</b>`;
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
@endsection