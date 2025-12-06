@extends('Layout.app')

@section('title', 'Apps')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-10">
        @include('Layout.msgStatus')
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                Apps Registered
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-light btn-sm ms-1" id="reloadBtn"><i class="bi bi-arrow-clockwise"></i> REFRESH</button>
                    <a href="{{ route('apps.generate') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-terminal"></i> APP</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-hover text-center dataTable no-footer" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Licenses Count</th>
                                <th>Created</th>
                                <th>Registrar</th>
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
                    url: '{{ route('apps.data') }}',
                    type: 'GET',
                    dataSrc: 'data'
                },
                columns: [
                    { data: 'id' },
                    { data: 'name' },
                    { data: 'price' },
                    { data: 'licenses' },
                    { data: 'created' },
                    { data: 'registrar' },
                    {
                        data: 'ids',
                        render: function(data, type, row) {
                            let url = `{{ route('apps.edit') }}/${data[0]}`;
                            return `
                            <button type="button" class="btn btn-outline-dark btn-sm copy-trigger" data-copy="${data[1]}" data-name="${data[2]}"><i class="bi bi-clipboard"></i></button>
                            <a href='${url}' class="btn btn-outline-dark btn-sm"><i class="bi bi-pencil-square"></i></a>
                            `;
                        }
                    }
                ],
                columnDefs: [
                    { targets: [4], searchable: false },
                    { targets: [0, 1, 2, 3], searchable: true },
                    { targets: [5], visible: false, searchable: true },
                    { orderable: false, targets: -1 }
                ]
            });

            $('#reloadBtn').on('click', function () {
                table.ajax.reload(null, false);
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
@endsection