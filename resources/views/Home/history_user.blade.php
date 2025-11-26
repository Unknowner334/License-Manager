@extends('Layout.app')

@section('title', 'Users')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-12">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header text-bg-dark">
                <div class="row">
                    <div class="col pt-1">
                        Users History
                    </div>
                    <div class="col text-end">
                        <a class="btn btn-outline-light btn-sm" href={{ route('admin.users') }}><i class="bi bi-person"></i> BACK</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped table-hover text-center">
                        <tr>
                            <th><span class="align-middle badge text-dark fs-6">#</span></th>
                            <th><span class="align-middle badge text-dark fs-6">User ID</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Username</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Status</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Type</span></th>
                            <th><span class="align-middle badge text-dark fs-6">IP Address</span></th>
                            <th><span class="align-middle badge text-dark fs-6">User Agent</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Payload</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Created At</span></th>
                        </tr>
                        @if ($histories->isNotEmpty())
                            @foreach ($histories as $history)
                                @php
                                    if ($history->user_id == NULL) {
                                        $user_id = "N/A";
                                    } else {
                                        $user_id = Controller::censorText($history->user_id, 3);
                                    }
                                @endphp
                                <tr>
                                    <td><span class="align-middle badge text-dark fs-6">{{ ($histories->currentPage() - 1) * $histories->perPage() + $loop->iteration }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6 copy-trigger" data-copy="{{ $history->user_id }}">{{ $user_id }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6 copy-trigger" data-copy="{{ $history->username }}">{{ Controller::censorText($history->username, 2) }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ $history->status }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ $history->type }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6 copy-trigger" data-copy="{{ $history->ip_address }}">{{ $history->ip_address }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6 copy-trigger" data-copy="{{ $history->user_agent }}">{{ Controller::censorText($history->user_agent, 10) }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ Controller::timeElapsed($history->created_at) }}</span></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8"><span class="align-middle badge text-danger fs-6">No Users History Where Found</span></td>
                            </tr>
                        @endif
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $histories->onEachSide(1)->links('pagination::bootstrap-5') }}
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