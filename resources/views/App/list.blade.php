@extends('Layout.app')

@section('title', 'Apps')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-10">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header text-bg-dark">
                <div class="row">
                    <div class="col pt-1">
                        Apps Registration
                    </div>
                    <div class="col text-end">
                        <a class="btn btn-outline-light btn-sm" href={{ route('apps.generate') }}><i class="bi bi-terminal"></i> APP</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped table-hover text-center">
                        <tr>
                            <th><span class="align-middle badge text-dark fs-6">#</span></th>
                            <th><span class="align-middle badge text-dark fs-6">App ID</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Name</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Basic</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Premium</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Created</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Action</span></th>
                        </tr>
                        @if ($apps->isNotEmpty())
                            @foreach ($apps as $app)
                                <tr>
                                    <td><span class="align-middle badge text-dark fs-6">{{ $loop->iteration }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ Controller::censorText($app->app_id) }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::statusColor($app->status) }} fs-6">{{ $app->name }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ $app->ppd_basic }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ $app->ppd_premium }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ Controller::timeElapsed($app->created_at) }}</span></td>
                                    <td>
                                        <a href={{ route('apps.edit', ['id' => $app->edit_id]) }} class="btn btn-outline-dark">
                                            <i class="bi bi-person"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7"><span class="align-middle badge text-danger fs-6">No Apps Where Found</span></td>
                            </tr>
                        @endif
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $apps->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection