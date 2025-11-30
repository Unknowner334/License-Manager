@extends('Layout.app')

@section('title', 'Apps')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-12">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="h6 mb-0">Apps Registration</span>
                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('apps') }}" method="get" class="d-flex align-items-center gap-1 mb-0">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search App">
                        <button type="submit" class="btn btn-outline-light btn-sm">Go</button>
                    </form>
                    <a href="{{ route('apps.generate') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-terminal"></i> APP</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover text-center">
                        <tr>
                            <th><span class="align-middle badge text-dark fs-6">#</span></th>
                            <th><span class="align-middle badge text-dark fs-6">App ID</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Name</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Basic</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Premium</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Created</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Created By</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Keys Count</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Action</span></th>
                        </tr>
                        @if ($apps->isNotEmpty())
                            @foreach ($apps as $app)
                                @php
                                    $keysCount = 0;

                                    foreach($app->keys as $key) {
                                        $keysCount += 1;
                                    }
                                @endphp
                                <tr>
                                    <td><span class="align-middle badge text-dark fs-6">{{ ($apps->currentPage() - 1) * $apps->perPage() + $loop->iteration }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6 copy-trigger" data-copy="{{ $app->app_id }}">{{ Controller::censorText($app->app_id) }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::statusColor($app->status) }} fs-6">{{ $app->name }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ number_format($app->ppd_basic) }}{{ $currency }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ number_format($app->ppd_premium) }}{{ $currency }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ Controller::timeElapsed($app->created_at) }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ Controller::userUsername($app->created_by) }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ number_format($keysCount) }}</span></td>
                                    <td>
                                        <a href={{ route('apps.edit', ['id' => $app->edit_id]) }} class="btn btn-outline-dark">
                                            <i class="bi bi-person"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8"><span class="align-middle badge text-danger fs-6">No Apps Where Found</span></td>
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function fallbackCopy(text) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    document.execCommand('copy');
                    console.log(`Copied (fallback): ${text}`);
                } catch (err) {
                    console.error('Fallback copy failed:', err);
                }
                document.body.removeChild(textarea);
            }

            document.querySelectorAll('.copy-trigger').forEach(el => {
                el.addEventListener('click', () => {
                    const text = el.getAttribute('data-copy');
                    if (!text) return;

                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text)
                            .then(() => console.log(`Copied: ${text}`))
                            .catch(() => fallbackCopy(text));
                    } else {
                        fallbackCopy(text);
                    }
                });
            });
        });
    </script>
@endsection