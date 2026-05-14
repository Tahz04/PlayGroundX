@extends('layouts.app')

@section('title', 'Quản lý yêu cầu chủ sân')

@section('content')
<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fas fa-user-tie text-primary me-2"></i>Yêu Cầu Trở Thành Chủ Sân</h2>
            <p class="text-muted mb-0">Xem xét và phê duyệt yêu cầu từ người dùng</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Người dùng</th>
                        <th>Lời nhắn</th>
                        <th>Giấy tờ</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $ownerRequest)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $ownerRequest->id }}</td>
                            <td>
                                <div class="fw-bold">{{ $ownerRequest->user->name }}</div>
                                <div class="small text-muted">{{ $ownerRequest->user->email }}</div>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $ownerRequest->message ?? '—' }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($ownerRequest->image_1)
                                        @php $ext1 = strtolower(pathinfo($ownerRequest->image_1, PATHINFO_EXTENSION)); @endphp
                                        @if($ext1 === 'pdf')
                                            <a href="{{ Storage::url($ownerRequest->image_1) }}" target="_blank"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-file-pdf text-danger me-1"></i>Mặt trước
                                            </a>
                                        @else
                                            <a href="{{ Storage::url($ownerRequest->image_1) }}" target="_blank"
                                               title="Mặt trước">
                                                <img src="{{ Storage::url($ownerRequest->image_1) }}"
                                                     width="64" height="64"
                                                     class="rounded border object-fit-cover"
                                                     style="object-fit:cover; cursor:pointer;"
                                                     alt="Mặt trước">
                                            </a>
                                        @endif
                                    @endif

                                    @if($ownerRequest->image_2)
                                        @php $ext2 = strtolower(pathinfo($ownerRequest->image_2, PATHINFO_EXTENSION)); @endphp
                                        @if($ext2 === 'pdf')
                                            <a href="{{ Storage::url($ownerRequest->image_2) }}" target="_blank"
                                               class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-file-pdf text-danger me-1"></i>Mặt sau
                                            </a>
                                        @else
                                            <a href="{{ Storage::url($ownerRequest->image_2) }}" target="_blank"
                                               title="Mặt sau">
                                                <img src="{{ Storage::url($ownerRequest->image_2) }}"
                                                     width="64" height="64"
                                                     class="rounded border"
                                                     style="object-fit:cover; cursor:pointer;"
                                                     alt="Mặt sau">
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-muted small fst-italic">Chưa có</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($ownerRequest->status) {
                                        'pending'  => 'bg-warning text-dark',
                                        'approved' => 'bg-success text-white',
                                        'rejected' => 'bg-danger text-white',
                                        default    => 'bg-secondary text-white',
                                    };
                                    $statusLabel = match($ownerRequest->status) {
                                        'pending'  => 'Chờ duyệt',
                                        'approved' => 'Đã duyệt',
                                        'rejected' => 'Từ chối',
                                        default    => $ownerRequest->status,
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2">{{ $statusLabel }}</span>
                            </td>
                            <td class="small text-muted">{{ $ownerRequest->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end pe-4">
                                @if($ownerRequest->status === 'pending')
                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="{{ route('admin.owner-requests.approve', $ownerRequest) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3"
                                                    onclick="return confirm('Duyệt yêu cầu của {{ $ownerRequest->user->name }}?')">
                                                <i class="fas fa-check me-1"></i> Duyệt
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.owner-requests.reject', $ownerRequest) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                    onclick="return confirm('Từ chối yêu cầu của {{ $ownerRequest->user->name }}?')">
                                                <i class="fas fa-times me-1"></i> Từ chối
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Đã xử lý</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-user-tie fa-2x text-muted mb-3 d-block opacity-50"></i>
                                <span class="text-muted">Chưa có yêu cầu nào.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: .72rem;
    letter-spacing: .05em;
    color: #64748b;
    padding-top: 1.1rem;
    padding-bottom: 1.1rem;
    border-bottom: 2px solid #f1f5f9;
}
.table tbody td {
    padding-top: .9rem;
    padding-bottom: .9rem;
    border-bottom: 1px solid #f1f5f9;
}
.table-hover tbody tr:hover { background: #f8fafc; }
</style>
@endsection
