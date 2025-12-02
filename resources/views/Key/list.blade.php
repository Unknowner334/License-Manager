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
                    <button class="btn btn-secondary btn-sm ms-1" id="blur-out" data-bs-toggle="tooltip" data-bs-placement="top" title="Eye Protect"><i class="bi bi-eye-slash"></i></button>
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
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Registrar</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Price</span></th>
                                    <th><span class="align-middle badge fw-semibold text-dark fs-6">Action</span></th>
                                </tr>
                            </thead>
                            @foreach ($keys as $item)
                                @php
                                    if ($item->owner == NULL) {
                                        $owner = "N/A";
                                    } else {
                                        $owner = $item->owner;
                                    }

                                    $price = number_format(KeyController::keyPriceCalculator($item->app->price, $item->max_devices, $item->duration));
                                    $raw_price = KeyController::keyPriceCalculator($item->app->price, $item->max_devices, $item->duration);

                                    if ($raw_price < 10000) {
                                        $price = $price;
                                    } else if ($raw_price >= 10000 && $raw_price < 1000000) {
                                        $price = number_format($raw_price / 1000) . 'k';
                                    } else if ($raw_price >= 1000000 && $raw_price < 1000000000) {
                                        $price = number_format($raw_price / 1000000) . 'm';
                                    } else if ($raw_price >= 1000000000 && $raw_price < 1000000000000) {
                                        $price = number_format($raw_price / 1000000000) . 'b';
                                    } else if ($raw_price >= 1000000000000) {
                                        $price = number_format($raw_price / 1000000000000) . 't';
                                    } else {
                                        $price = "N/A";
                                    }
                                @endphp
                                <tr>
                                    <td><span class="align-middle badge fw-semibold text-dark fs-6">{{ $item->id }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-dark fs-6">{{ $owner }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-{{ Controller::statusColor($item->app->status) }} fs-6">{{ $item->app->name ?? 'N/A' }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-{{ Controller::statusColor($item->status) }} fs-6 blur Blur">{{ $item->key }}</span></td>
                                    <td><span class="align-middle badge fw-semibold text-{{ KeyController::RemainingDaysColor(KeyController::RemainingDays($item->expire_date)) }} fs-6">{{ KeyController::RemainingDays($item->expire_date) }}/{{ $item->duration ?? 'N/A' }} Days</span></td>
                                    <td><span class="align-middle badge fw-semibold text-dark fs-6">{{ KeyController::DevicesHooked($item->devices) }}/{{ $item->max_devices ?? 'N/A' }}</span></td>
                                    <td><i class="align-middle badge fw-semibold text-dark fs-6">{{ Controller::timeElapsed($item->created_at) ?? 'N/A' }}</i></td>
                                    <td><span class="align-middle badge fw-semibold text-dark fs-6">{{ Controller::userUsername($item->registrar) }}</span></td>
                                    <td title="{{ number_format($raw_price) . $currency }}"><span class="align-middle badge fw-semibold text-dark fs-6">{{ $price . $currency }}</span></td>
                                    <td>
                                        <a href={{ route('keys.resetApiKey', ['id' => $item->edit_id]) }} class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-bootstrap-reboot"></i>
                                        </a>

                                        <a href={{ route('keys.edit', ['id' => $item->edit_id]) }} class="btn btn-outline-dark btn-sm">
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
                    { targets: [6, 9], searchable: false },
                    { targets: [0, 1, 2, 3, 4, 5, 7, 8], searchable: true },
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