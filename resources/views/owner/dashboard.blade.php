@extends('layouts.app')

@section('title', 'Dashboard Chủ Sân - PlayGroundX')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5 align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill py-2 px-3 mb-3">Dashboard Chủ Sân</span>
                <h1 class="display-6 mb-3">Quản lý sân của bạn</h1>
                <p class="text-muted fs-6">Theo dõi thông tin sân, đơn đặt sân và quản lý lịch biểu của bạn.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('owner.arenas.create') }}" class="btn btn-hero-primary btn-lg">
                    <i class="fas fa-plus me-2"></i> Thêm Sân Mới
                </a>
                <a href="{{ route('owner.bookings.index') }}" class="btn btn-outline-primary btn-lg ms-2">
                    <i class="fas fa-list me-2"></i> Danh Sách Đơn
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 rounded-4 shadow-sm bg-white h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <p class="text-muted mb-0 small">Tổng Sân</p>
                                <h3 class="mb-0">{{ $totalArenas }}</h3>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-circle p-3" style="font-size: 1.5rem;">
                                <i class="fas fa-futbol"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 rounded-4 shadow-sm bg-white h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <p class="text-muted mb-0 small">Tổng Đơn Đặt</p>
                                <h3 class="mb-0">{{ $totalBookings }}</h3>
                            </div>
                            <span class="badge bg-info bg-opacity-10 text-info rounded-circle p-3" style="font-size: 1.5rem;">
                                <i class="fas fa-calendar-check"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 rounded-4 shadow-sm bg-white h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <p class="text-muted mb-0 small">Chờ Xác Nhận</p>
                                <h3 class="mb-0 text-warning">{{ $pendingBookings }}</h3>
                            </div>
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-circle p-3" style="font-size: 1.5rem;">
                                <i class="fas fa-hourglass-half"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="card border-0 rounded-4 shadow-sm bg-white h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <p class="text-muted mb-0 small">Đã Xác Nhận</p>
                                <h3 class="mb-0 text-success">{{ $confirmedBookings }}</h3>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-circle p-3" style="font-size: 1.5rem;">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Arenas List -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 rounded-4 shadow-sm bg-white">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Các Sân Của Bạn</h5>
                        @if($arenas->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Tên Sân</th>
                                            <th>Loại</th>
                                            <th>Địa Điểm</th>
                                            <th>Giá</th>
                                            <th>Trạng Thái</th>
                                            <th>Hành Động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($arenas as $arena)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $arena->name }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">{{ $arena->type }}</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ Str::limit($arena->location, 30) }}</small>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-primary">{{ number_format($arena->price, 0, ',', '.') }} đ</span>
                                                </td>
                                                <td>
                                                    @if($arena->status)
                                                        <span class="badge bg-success text-white">Hoạt động</span>
                                                    @else
                                                        <span class="badge bg-secondary text-white">Ẩn</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('owner.arenas.edit', $arena) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-edit me-1"></i> Sửa
                                                        </a>
                                                        <form action="{{ route('owner.arenas.destroy', $arena) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa sân này?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash me-1"></i> Xóa
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-25"></i>
                                <p class="text-muted mb-3">Bạn chưa có sân nào.</p>
                                <a href="{{ route('owner.arenas.create') }}" class="btn btn-hero-primary">Thêm Sân Ngay</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
