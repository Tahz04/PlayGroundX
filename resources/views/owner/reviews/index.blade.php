@extends('layouts.app')

@section('title', 'Quản Lý Đánh Giá - Chủ Sân')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col">
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill py-2 px-3 mb-3">Quản Lý Đánh Giá</span>
                <h1 class="display-6 mb-3">Đánh Giá Của Khách Hàng</h1>
                <p class="text-muted">Xem các đánh giá trên các sân của bạn.</p>
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
                <form method="GET" action="{{ route('owner.reviews.index') }}" class="d-flex align-items-center gap-3 flex-wrap">
                    <label class="form-label mb-0">Sân:</label>
                    <select name="arena_id" class="form-select" style="width: 200px;">
                        <option value="">Tất cả sân</option>
                        @foreach($arenas as $arena)
                            <option value="{{ $arena->id }}" {{ $filterArena == $arena->id ? 'selected' : '' }}>
                                {{ $arena->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-filter me-1"></i>Lọc
                    </button>
                </form>
            </div>
        </div>

        <!-- Reviews List -->
        <div class="row g-4">
            @forelse($reviews as $review)
                <div class="col-lg-8 mx-auto">
                    <div class="card border-0 rounded-4 shadow-sm p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                     style="width:50px;height:50px;background:linear-gradient(135deg,#10b981,#059669);font-size:1rem;flex-shrink:0;">
                                    {{ mb_strtoupper(mb_substr($review->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold fs-5">{{ $review->user->name }}</div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex text-warning" style="font-size: 0.95rem;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                            @endfor
                                        </div>
                                        <span class="fw-bold">{{ $review->rating }}/5</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted d-block">{{ $review->created_at->diffForHumans() }}</small>
                                @if($review->status === 'approved')
                                    <span class="badge bg-success text-white mt-1">Đã duyệt</span>
                                @elseif($review->status === 'pending')
                                    <span class="badge bg-warning text-dark mt-1">Chờ duyệt</span>
                                @else
                                    <span class="badge bg-danger text-white mt-1">Từ chối</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $review->arena->name }}</span>
                        </div>

                        <p class="text-dark mb-4" style="line-height: 1.8;">{{ $review->comment }}</p>

                        <!-- Report Button -->
                        @if($review->status === 'approved')
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill report-button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#reportModal"
                                    data-review-id="{{ $review->id }}"
                                    data-review-arena="{{ $review->arena->name }}"
                                    data-review-user="{{ $review->user->name }}">
                                <i class="fas fa-flag me-1"></i> Báo cáo
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 rounded-4 shadow-sm p-5 text-center">
                        <i class="fas fa-star display-4 text-muted mb-3 opacity-25"></i>
                        <p class="text-muted mb-0">Chưa có đánh giá nào trên sân của bạn.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($reviews->hasPages())
            <div class="mt-4">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</section>

<!-- Single Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Báo cáo đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reportForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Báo cáo review của <strong id="reportUserName"></strong> trên sân <strong id="reportArenaName"></strong>.</p>
                    <div class="mb-3">
                        <label class="form-label">Lý do báo cáo:</label>
                        <textarea name="reason" class="form-control rounded-3" rows="4" placeholder="Mô tả chi tiết lý do báo cáo..." required></textarea>
                    </div>
                    <p class="text-muted small">Admin sẽ xem xét báo cáo của bạn sớm nhất có thể.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger rounded-pill">
                        <i class="fas fa-flag me-1"></i> Báo cáo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.report-button').forEach(function (button) {
            button.addEventListener('click', function () {
                var reviewId = this.dataset.reviewId;
                var arenaName = this.dataset.reviewArena;
                var userName = this.dataset.reviewUser;
                var form = document.getElementById('reportForm');

                form.action = '/owner/reviews/' + reviewId + '/report';
                document.getElementById('reportArenaName').textContent = arenaName;
                document.getElementById('reportUserName').textContent = userName;
            });
        });
    });
</script>
@endpush
@endsection
