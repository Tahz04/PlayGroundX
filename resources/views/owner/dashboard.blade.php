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
            <div class="col-lg-4 text-lg-end d-flex flex-wrap justify-content-lg-end gap-2">
                <a href="{{ route('owner.arenas.create') }}" class="btn btn-hero-primary">
                    <i class="fas fa-plus me-2"></i> Thêm Sân
                </a>
                <a href="{{ route('owner.bookings.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list me-2"></i> Đơn Đặt
                </a>
                <a href="{{ route('owner.negotiations.index') }}" class="btn btn-outline-success">
                    <i class="fas fa-handshake me-2"></i> Thương Lượng
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

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

        <!-- Thu nhập theo tháng -->
        <div class="card border-0 rounded-4 shadow-sm bg-white mb-5">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div>
                        <h5 class="mb-1"><i class="fas fa-chart-line text-success me-2"></i>Thu Nhập Theo Tháng</h5>
                        <p class="text-muted mb-0 small">Tổng doanh thu từ các đơn đã thanh toán</p>
                    </div>
                    <form method="GET" action="{{ route('owner.dashboard') }}" class="d-flex align-items-center gap-2">
                        <input type="month" name="month" class="form-control form-control-sm"
                               value="{{ $selectedMonth }}" style="width: auto;">
                        <button type="submit" class="btn btn-sm btn-primary px-3">
                            <i class="fas fa-filter me-1"></i>Lọc
                        </button>
                    </form>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="p-4 rounded-4 text-center" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <p class="text-white mb-1 small opacity-75">Tổng thu nhập tháng</p>
                            <h2 class="text-white fw-bold mb-0">{{ number_format($monthlyIncome) }} <span class="fs-5">đ</span></h2>
                            <p class="text-white opacity-75 mb-0 small mt-1">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->locale('vi')->isoFormat('MMMM Y') }}
                            </p>
                        </div>
                    </div>
                    @if($incomeByArena->isNotEmpty())
                    <div class="col-md-8">
                        <h6 class="mb-3 text-muted">Chi tiết theo sân</h6>
                        @foreach($arenas as $arena)
                            @php $arenaIncome = $incomeByArena->get($arena->id); @endphp
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $arena->isActive() ? 'bg-success' : ($arena->isMaintenance() ? 'bg-warning text-dark' : 'bg-secondary') }} rounded-pill" style="width:10px;height:10px;padding:0;"></span>
                                    <span class="fw-semibold small">{{ $arena->name }}</span>
                                </div>
                                <span class="fw-bold text-primary small">
                                    {{ $arenaIncome ? number_format($arenaIncome->total) . ' đ' : '—' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Arenas List -->
        <div class="card border-0 rounded-4 shadow-sm bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Các Sân Của Bạn</h5>
                    <a href="{{ route('owner.arenas.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        Quản lý sân <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
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
                                            @if($arena->maintenance_note && $arena->isMaintenance())
                                                <div class="small text-warning"><i class="fas fa-info-circle me-1"></i>{{ $arena->maintenance_note }}</div>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-light text-dark">{{ $arena->type }}</span></td>
                                        <td><small class="text-muted">{{ Str::limit($arena->location, 30) }}</small></td>
                                        <td><span class="fw-bold text-primary">{{ number_format($arena->price, 0, ',', '.') }} đ</span></td>
                                        <td>
                                            @if($arena->isActive())
                                                <span class="badge bg-success text-white">Hoạt động</span>
                                            @elseif($arena->isMaintenance())
                                                <span class="badge bg-warning text-dark">Bảo trì</span>
                                            @else
                                                <span class="badge bg-secondary text-white">Ẩn</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('owner.arenas.edit', $arena) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit me-1"></i> Sửa
                                                </a>
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
</section>
@endsection
