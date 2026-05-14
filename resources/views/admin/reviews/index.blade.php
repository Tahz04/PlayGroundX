@extends('layouts.app')

@section('title', 'Quản Lý Đánh Giá - Admin')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill py-2 px-3 mb-3">Quản Lý Đánh Giá</span>
                <h1 class="display-6 mb-3">Duyệt Đánh Giá Sân</h1>
                <p class="text-muted">Xem xét và duyệt/từ chối các đánh giá từ khách hàng.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Filter -->
        <div class="card border-0 rounded-4 shadow-sm bg-white mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.reviews.index') }}" class="d-flex align-items-center gap-3 flex-wrap">
                    <label class="form-label mb-0">Trạng thái:</label>
                    <select name="status" class="form-select" style="width: 150px;">
                        <option value="all" {{ $filterStatus === 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="pending" {{ $filterStatus === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="approved" {{ $filterStatus === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="rejected" {{ $filterStatus === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-filter me-1"></i>Lọc
                    </button>
                </form>
            </div>
        </div>

        <!-- Reviews Table -->
        <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Khách Hàng</th>
                            <th>Sân</th>
                            <th>Đánh Giá</th>
                            <th>Nhận Xét</th>
                            <th>Trạng Thái</th>
                            <th class="text-end pe-4">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder me-2">{{ substr($review->user->name, 0, 1) }}</div>
                                        <div>
                                            <div class="fw-bold">{{ $review->user->name }}</div>
                                            <small class="text-muted">{{ $review->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $review->arena->name }}</span>
                                </td>
                                <td>
                                    <div class="d-flex text-warning" style="font-size: 0.9rem;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                    <span class="badge bg-info">{{ $review->rating }}/5</span>
                                </td>
                                <td>
                                    <div class="text-muted" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $review->comment }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($review->status) {
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-success text-white',
                                            'rejected' => 'bg-danger text-white',
                                            default => 'bg-secondary'
                                        };
                                        $statusText = match($review->status) {
                                            'pending' => 'Chờ duyệt',
                                            'approved' => 'Đã duyệt',
                                            'rejected' => 'Từ chối',
                                            default => $review->status
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} rounded-pill px-3">{{ $statusText }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    @if($review->status === 'pending')
                                        <div class="d-flex justify-content-end gap-2">
                                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" title="Duyệt">
                                                    <i class="fas fa-check me-1"></i> Duyệt
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3" title="Từ chối">
                                                    <i class="fas fa-times me-1"></i> Từ chối
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Xóa đánh giá này?')">
                                                <i class="fas fa-trash-alt me-1"></i> Xóa
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open display-4 d-block mb-3 opacity-25"></i>
                                    Chưa có đánh giá nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reviews->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

<style>
    .avatar-placeholder {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
        font-size: 0.85rem;
    }
    .table thead th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #6c757d;
        border-bottom-width: 1px;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection
