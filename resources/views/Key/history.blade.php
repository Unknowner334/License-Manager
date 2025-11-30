@extends('Layout.app')

@section('title', 'Keys')

@php
    use App\Models\App;
    use App\Models\Key;
    use App\Http\Controllers\KeyController;
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-12">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="h6 mb-0">Keys History</span>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal"><i class="bi bi-trash3"></i> DELETE ALL</button>
                    <form action={{ route('keys.history.delete.all') }} method="post" id="deleteForm">
                        @csrf
                        <input type="hidden" name="id" id="id" value="{{ $id }}">
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover text-center">
                        <tr>
                            <th><span class="align-middle badge text-dark fs-6">#</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Key ID</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Key</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Device</span></th>
                            <th><span class="align-middle badge text-dark fs-6">IP Address</span></th>
                            <th><span class="align-middle badge text-dark fs-6">App ID</span></th>
                            <th><span class="align-middle badge text-dark fs-6">App Name</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Status</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Type</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Created</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Action</span></th>
                        </tr>
                        @if ($keyHistory->isNotEmpty())
                            @foreach ($keyHistory as $History)
                                @php
                                    $app = App::where('app_id', $History->app_id)->first();
                                    $key = Key::where('edit_id', $History->key_id)->first();
                                @endphp
                                <tr>
                                    <td><span class="align-middle badge text-dark fs-6">{{ ($keyHistory->currentPage() - 1) * $keyHistory->perPage() + $loop->iteration }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::statusColor($key->status) }} fs-6 copy-trigger" data-copy="{{ $History->key_id }}">{{ Controller::censorText($History->key_id) }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::statusColor($key->status) }} fs-6 copy-trigger" data-copy="{{ $History->key }}">{{ Controller::censorText($History->key) }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6 copy-trigger" data-copy="{{ $History->device }}">{{ Controller::censorText($History->device) }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6 copy-trigger" data-copy="{{ $History->ip_address }}">{{ $History->ip_address }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::statusColor($app->status) }} fs-6 copy-trigger" data-copy="{{ $History->app_id }}">{{ Controller::censorText($History->app_id) }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::statusColor($app->status) }} fs-6 copy-trigger" data-copy="{{ $app->name ?? "N/A" }}">{{ $app->name ?? "N/A" }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ $History->status }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ $History->type }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ Controller::timeElapsed($History->created_at) ?? 'N/A' }}</span></td>
                                    <td>
                                        <form action={{ route('keys.history.delete') }} method="post">
                                            @csrf
                                            <input type="hidden" name="key_id" id="key_id" value="{{ $History->key_id }}">
                                            <input type="hidden" name="id" id="id" value="{{ $History->id }}">
                                            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="11"><span class="align-middle badge text-danger fs-6">No Key History Where Found</span></td>
                            </tr>
                        @endif
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $keyHistory->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        <p class="text-muted text-center">
            <a href="{{ route('keys') }}" class="py-1 px-2 bg-white text-muted"><small><i class="bi bi-arrow-left"></i> Back to Keys</small></a>
        </p>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header text-bg-danger">
                    <h5 class="modal-title">Confirm Generate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the key history?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            document.getElementById('deleteForm').submit();
        });

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