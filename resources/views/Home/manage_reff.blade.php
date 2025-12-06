@extends('Layout.app')

@section('title', 'Referrables Code')

@php
    use App\Http\Controllers\Controller;
    use App\Http\Controllers\DashController;
@endphp

@section('content')
    <div class="col-lg-10">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                Referrables Registered
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-light btn-sm ms-1" id="reloadBtn"><i class="bi bi-arrow-clockwise"></i> REFRESH</button>
                    <a class="btn btn-outline-light btn-sm" href={{ route('admin.referrable.generate') }}><i class="bi bi-person-add"></i> REFF</a>
                    <button class="btn btn-secondary btn-sm ms-1" id="blur-out" data-bs-toggle="tooltip" data-bs-placement="top" title="Eye Protect"><i class="bi bi-eye-slash"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-hover text-center dataTable no-footer" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Status</th>
                                <th>Users Count</th>
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
                    url: '{{ route('admin.referrable.data') }}',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'id' },
                    { data: 'code' },
                    { data: 'status' },
                    { data: 'users' },
                    { data: 'registrar' },
                    { data: 'created' },
                    {
                        data: 'edit_id',
                        render: function(data, type, row) {
                            let url = `{{ route('admin.referrable.edit') }}/${data}`
                            return `
                            <a href="${url}" class="btn btn-outline-dark btn-sm">
                                <i class="bi bi-pencil-square"></i>
                            </a>`;
                        }
                    },
                ],
                columnDefs: [
                    { targets: [5, 6], searchable: false },
                    { targets: [0, 1, 2, 3], searchable: true },
                    { targets: [4], visible: false, searchable: true },
                    { orderable: false, targets: -1 }
                ]
            });

            $('#reloadBtn').on('click', function () {
                table.ajax.reload(null, false);
            });

            $(document).on('click', '.copy-trigger', async function() {
                const copy = $(this).data('copy');

                const code = await copyToClipboard(copy);

                let message = "";
                let icon = "error";

                switch (code) {
                    case 0:
                        message = `<b>Reff</b> ${copy} <b>Successfully Copied</b>`;
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