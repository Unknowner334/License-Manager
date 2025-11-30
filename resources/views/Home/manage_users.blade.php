@extends('Layout.app')

@section('title', 'Users')

@php
    use App\Http\Controllers\Controller;
@endphp

@section('content')
    <div class="col-lg-12">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="h6 mb-0">Users Registration</span>
                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('admin.users') }}" method="get" class="d-flex align-items-center gap-1 mb-0">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search User">
                        <button type="submit" class="btn btn-outline-light btn-sm">Go</button>
                    </form>
                    <a class="btn btn-outline-light btn-sm" href={{ route('admin.users.history') }}><i class="bi bi-person"></i> HISTORY</a>
                    <a class="btn btn-outline-light btn-sm" href={{ route('admin.users.generate') }}><i class="bi bi-person"></i> USER</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover text-center">
                        <tr>
                            <th><span class="align-middle badge text-dark fs-6">#</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Name</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Username</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Permissions</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Reff</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Last Login</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Created By</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Created At</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Action</span></th>
                        </tr>
                        @if ($users->isNotEmpty())
                            @foreach ($users as $user)
                                @php
                                    if ($user->referrable != NULL) {
                                        $reff_status = Controller::statusColor($user->referrable->status);
                                        $reff_code = Controller::censorText($user->referrable->code);
                                    } else {
                                        $reff_status = 'dark';
                                        $reff_code = NULL;
                                    }
                                @endphp
                                <tr>
                                    <td><span class="align-middle badge text-dark fs-6">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::statusColor($user->status) }} fs-6">{{ $user->name }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::statusColor($user->status) }} fs-6 copy-trigger" data-copy="{{ $user->username }}">{{ Controller::censorText($user->username, 2) }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::permissionColor($user->permissions) }} fs-6">{{ $user->permissions }}</span></td>
                                    <td><span class="align-middle badge text-{{ $reff_status }} fs-6">{{ $reff_code ?? "N/A" }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ Controller::timeElapsed($user->last_login) ?? "N/A" }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ Controller::userUsername($user->created_by) }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ Controller::timeElapsed($user->created_at) ?? "N/A" }}</span></td>
                                    <td>
                                        <a href={{ route('admin.users.edit', ['id' => $user->user_id]) }} class="btn btn-outline-dark">
                                            <i class="bi bi-person"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10"><span class="align-middle badge text-danger fs-6">No Users Where Found</span></td>
                            </tr>
                        @endif
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
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