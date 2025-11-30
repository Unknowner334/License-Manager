@extends('Layout.app')

@section('title', 'Keys')

@php
    use App\Http\Controllers\KeyController;
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-12">
        @include('Layout.msgStatus')
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                Keys Registered
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('keys.generate') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-key"></i> KEY</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    @if ($keys->isNotEmpty())
                        <table id="datatable" class="table table-sm table-bordered table-hover text-center" style="width:100%">
                            <thead>
                                <tr>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">#</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Owner</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">App</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Key</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Duration</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Devices</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Created</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Created By</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Price</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Action</span></th>
                                </tr>
                            </thead>
                            @foreach ($keys as $key)
                                @php
                                    if ($key->owner == "") {
                                        $owner = "N/A";
                                    } else {
                                        $owner = $key->owner;
                                    }
                                @endphp
                                <tr>
                                    <td><span class="align-middle badge fw-semibold text-dark fs-6">{{ $loop->iteration }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-dark fs-6">{{ $owner }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-{{ Controller::statusColor($key->app->status) }} fs-6">{{ $key->app->name ?? 'N/A' }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-{{ Controller::statusColor($key->status) }} fs-6">{{ $key->key }}</span></td>
                                    <td><i class="align-middle badge fw-semibold text-{{ KeyController::RemainingDaysColor(KeyController::RemainingDays($key->expire_date)) }} fs-6">{{ KeyController::RemainingDays($key->expire_date) }}/{{ $key->duration ?? 'N/A' }} Days</i></td>
                                    <td><i class="align-middle badge fw-semibold text-dark fs-6">{{ KeyController::DevicesHooked($key->edit_id) }}/{{ $key->max_devices ?? 'N/A' }}</i></td>
                                    <td><i class="align-middle badge fw-semibold text-dark fs-6">{{ Controller::timeElapsed($key->created_at) ?? 'N/A' }}</i></td>
                                    <td><span class="align-middle badge fw-semibold text-dark fs-6">{{ Controller::userUsername($key->created_by) }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-dark fs-6">{{ number_format(KeyController::keyPriceCalculator($key->rank, $key->app->ppd_basic, $key->app->ppd_premium, $key->max_devices, $key->duration)) }}{{ $currency }}</span></td>
                                    <td>
                                        <a href={{ route('keys.resetApiKey', ['id' => $key->edit_id]) }} class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-bootstrap-reboot"></i>
                                        </a>

                                        <a href={{ route('keys.edit', ['id' => $key->edit_id]) }} class="btn btn-outline-dark btn-sm">
                                            <i class="bi bi-person"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    @else
                        <table class="table table-sm table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th colspan="11"><span class="align-middle badge text-dark fs-6 fw-normal">There are no <strong>keys</strong> to show</span></th>
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
                    { orderable: false, targets: -1 }
                ]
            });
        });
    </script>
@endsection