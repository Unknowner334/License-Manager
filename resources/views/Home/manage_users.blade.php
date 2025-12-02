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
                    <a class="btn btn-outline-light btn-sm" href={{ route('admin.users.history') }}><i class="bi bi-person"></i> HISTORY</a>
                    <a class="btn btn-outline-light btn-sm" href={{ route('admin.users.generate') }}><i class="bi bi-person"></i> USER</a>
                    <button class="btn btn-secondary btn-sm ms-1" id="blur-out" data-bs-toggle="tooltip" data-bs-placement="top" title="Eye Protect"><i class="bi bi-eye-slash"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    @if ($users->isNotEmpty())
                        <table id="datatable" class="table table-sm table-bordered table-hover text-center" style="width:100%">
                            <thead>
                                <tr>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">#</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Name</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Username</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Saldo</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Role</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Reff</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Registrar</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Created</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Action</span></th>
                                </tr>
                            </thead>
                            @foreach ($users as $item)
                                @php
                                    if ($item->referrable != NULL) {
                                        $reff_status = Controller::statusColor($item->referrable->status);
                                        $reff_code = Controller::censorText($item->referrable->code);
                                    } else {
                                        $reff_status = 'dark';
                                        $reff_code = "N/A";
                                    }

                                    $saldo = Controller::saldoData($item->saldo, $item->permissions);
                                @endphp
                                <tr>
                                    <td><span class="align-middle badge fw-semibold text-dark fs-6">{{ $item->id }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-{{ Controller::statusColor($item->status) }} fs-6">{{ $item->name }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-{{ Controller::statusColor($item->status) }} fs-6 blur Blur">{{ $item->username }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-{{ $saldo[1] }} fs-6">{{ $saldo[0] }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-{{ Controller::permissionColor($item->permissions) }} fs-6">{{ $item->permissions }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-{{ $reff_status }} fs-6">{{ $reff_code }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-dark fs-6">{{ Controller::userUsername($item->registrar) }}</span></td>
                                    <td><i class="align-middle badge fw-semibold text-dark fs-6">{{ Controller::timeElapsed($item->created_at) }}</i></td>
                                    <td>
                                        <a href={{ route('admin.users.wallet', ['id' => $item->user_id]) }} class="btn btn-outline-dark btn-sm">
                                            <i class="bi bi-wallet"></i>
                                        </a>

                                        <a href={{ route('admin.users.history.user', ['id' => $item->user_id]) }} class="btn btn-outline-dark btn-sm">
                                            <i class="bi bi-person"></i>
                                        </a>

                                        <a href={{ route('admin.users.edit', ['id' => $item->user_id]) }} class="btn btn-outline-dark btn-sm">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <table class="table table-sm table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th colspan="9"><span class="align-middle badge text-dark fs-6 fw-normal">There are no <strong>users</strong> to show</span></th>
                                </tr>
                            </thead>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                pageLength: 10,
                lengthChange: true,
                ordering: true,
                order: [[0,'desc']],
                columnDefs: [
                    { targets: [7, 8], searchable: false },
                    { targets: [0, 1, 2, 4, 5, 6], searchable: true },
                    { orderable: false, targets: -1 }
                ]
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
        });
    </script>
@endsection