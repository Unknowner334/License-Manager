@extends('Layout.app')

@section('title', 'Referrables Code')

@php
    use App\Http\Controllers\Controller;
    use App\Http\Controllers\DashController;
@endphp

@section('content')
    <div class="col-lg-12">
        @include('Layout.msgStatus')
        <div class="card mb-5">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span class="h6 mb-0">Referrables Registration</span>
                <div class="d-flex align-items-center gap-2">
                    <a class="btn btn-outline-light btn-sm" href={{ route('admin.referrable.generate') }}><i class="bi bi-person-add"></i> REFF</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped table-hover text-center">
                        <tr>
                            <th><span class="align-middle badge text-dark fs-6">#</span></th>
                            <th><span class="align-middle badge text-dark fs-6">ID</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Code</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Status</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Users Count</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Created By</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Created At</span></th>
                            <th><span class="align-middle badge text-dark fs-6">Action</span></th>
                        </tr>
                        @if ($reffs->isNotEmpty())
                            @foreach ($reffs as $reff)
                                <tr>
                                    <td><span class="align-middle badge text-dark fs-6">{{ ($reffs->currentPage() - 1) * $reffs->perPage() + $loop->iteration }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::statusColor($reff->status) }} fs-6 copy-trigger" data-copy="{{ $reff->edit_id }}">{{ Controller::censorText($reff->edit_id) }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::statusColor($reff->status) }} fs-6 copy-trigger" data-copy="{{ $reff->code }}">{{ Controller::censorText($reff->code) }}</span></td>
                                    <td><span class="align-middle badge text-{{ Controller::statusColor($reff->status) }} fs-6">{{ $reff->status ?? "N/A" }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ DashController::UsersCreated($reff->edit_id) }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ Controller::userUsername($reff->created_by) }}</span></td>
                                    <td><span class="align-middle badge text-dark fs-6">{{ Controller::timeElapsed($reff->created_at) ?? "N/A" }}</span></td>
                                    <td>
                                        <a href={{ route('admin.referrable.edit', ['id' => $reff->edit_id]) }} class="btn btn-outline-dark">
                                            <i class="bi bi-person-add"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10"><span class="align-middle badge text-danger fs-6">No Reff Where Found</span></td>
                            </tr>
                        @endif
                    </table>
                </div>

                <div class="d-flex justify-content-end">
                    {{ $reffs->onEachSide(1)->links('pagination::bootstrap-5') }}
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