@extends('layouts.app')

@section('title', 'Danh Sách Sân Bóng Đá - PlayGroundX')

@section('content')
<!-- Hero Section -->
<section class="arenas-hero" style="background: var(--clr-dark-900); padding: 120px 0 60px; position: relative; overflow: hidden;">
    <div class="container relative z-10">
        <div class="text-center mb-5" data-aos="fade-up">
            <h1 class="display-4 fw-bold text-white mb-3">Tất Cả <span class="accent">Sân Bóng Đá</span></h1>
            <p class="text-muted fs-5 mx-auto" style="max-width: 700px;">Khám phá và đặt ngay sân bóng phù hợp với đội của bạn trong hàng trăm sân chất lượng nhất.</p>
        </div>

        <!-- Filter Bar -->
        <div class="filter-card shadow-lg p-3 p-md-4 mb-5" data-aos="zoom-in" data-aos-delay="100">
            <form action="{{ route('arenas.index') }}" method="GET" class="row g-3">
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Tìm tên sân hoặc khu vực..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select name="type" class="form-select">
                        <option value="">Tất cả loại sân</option>
                        <option value="Sân 5" {{ request('type') == 'Sân 5' ? 'selected' : '' }}>Sân 5 Người</option>
                        <option value="Sân 7" {{ request('type') == 'Sân 7' ? 'selected' : '' }}>Sân 7 Người</option>
                        <option value="Sân 11" {{ request('type') == 'Sân 11' ? 'selected' : '' }}>Sân 11 Người</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="fas fa-filter me-2"></i>Tìm Kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Listing Section -->
<section class="py-5 bg-light" style="min-height: 500px;">
    <div class="container">
        <div class="row g-4">
            @forelse($arenas as $arena)
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 100 }}">
                    <div class="pitch-card bg-white shadow-sm h-100">
                        <div class="pitch-image">
                            <img src="https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=400&h=250&fit=crop" alt="{{ $arena->name }}">
                            <span class="pitch-badge available"><i class="fas fa-check-circle me-1"></i>Sẵn sàng</span>
                            <span class="pitch-type-badge"><i class="fas fa-users me-1"></i>{{ $arena->type }}</span>
                            <div class="pitch-overlay"></div>
                        </div>
                        <div class="pitch-info p-3">
                            <h3 class="pitch-name h5 mb-2">{{ $arena->name }}</h3>
                            <div class="pitch-location mb-3 text-muted small">
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                <span>{{ Str::limit($arena->location, 50) }}</span>
                            </div>
                            <div class="pitch-meta d-flex justify-content-between align-items-center mb-3">
                                <div class="pitch-price text-primary fw-bold" style="font-size: 1.1rem;">{{ number_format($arena->price) }}đ <span class="text-muted small fw-normal">/ h</span></div>
                                <div class="pitch-rating small"><i class="fas fa-star text-warning me-1"></i>4.9</div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('map') }}?id={{ $arena->id }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-map-marked-alt me-1"></i>Bản đồ
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('bookings.create', $arena) }}" class="btn btn-primary btn-sm w-100 fw-bold">Đặt Sân</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="empty-state py-5" data-aos="fade-up">
                        <i class="fas fa-search-minus fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted">Không tìm thấy sân nào phù hợp</h3>
                        <p class="text-muted">Vui lòng thử lại với từ khóa khác hoặc xóa bộ lọc.</p>
                        <a href="{{ route('arenas.index') }}" class="btn btn-link mt-3 text-decoration-none">Xóa bộ lọc</a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $arenas->appends(request()->query())->links() }}
        </div>
    </div>
</section>

<style>
    .filter-card {
        background: white;
        border-radius: 20px;
        transform: translateY(30px);
        z-index: 20;
        position: relative;
    }
    .filter-card .form-control, .filter-card .form-select {
        height: 50px;
        border-radius: 12px;
    }
    .filter-card .btn-primary {
        height: 50px;
        border-radius: 12px;
    }
    .pagination .page-item .page-link {
        border-radius: 10px;
        margin: 0 5px;
        color: var(--clr-dark-700);
        border: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .pagination .page-item.active .page-link {
        background: var(--gradient-primary);
        color: white;
    }
</style>
@endsection
