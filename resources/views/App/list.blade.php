@extends('Layout.app')

@section('title', 'Apps')

@php
    use App\Http\Controllers\AppController;
@endphp

@section('content')
    <div class="col-lg-12">
        @include('Layout.msgStatus')
        <div class="row">
            <div class="col-lg-7">
                <div class="card mb-5">
                    <div class="card-header text-bg-dark">
                        Apps List
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped table-hover text-center">
                                <tr>
                                    <th><span class="align-middle badge text-dark fs-6">#</span></th>
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
                                            <td><span class="align-middle badge text-dark fs-6">{{ $app->name }}</span></td>
                                            <td><span class="align-middle badge text-dark fs-6">{{ $app->ppd_basic }}</span></td>
                                            <td><span class="align-middle badge text-dark fs-6">{{ $app->ppd_premium }}</span></td>
                                            <td><span class="align-middle badge text-dark fs-6">{{ AppController::timeElapsed($app->created_at) }}</span></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2"><span class="align-middle badge text-danger fs-6">No Keys Where Found</span></td>
                                    </tr>
                                @endif
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $apps->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card mb-5">
                    <div class="card-header text-bg-dark">
                        Apps Generate
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection