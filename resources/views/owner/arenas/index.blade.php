@extends('layouts.app')

@section('title', 'Danh Sách Sân - PlayGroundX')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5 align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill py-2 px-3 mb-3">Quản Lý Sân</span>
                <h1 class="display-6 mb-3">Danh Sách Sân Của Bạn</h1>
                <p class="text-muted fs-6">Xem và quản lý tất cả các sân bóng của bạn.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('owner.arenas.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i> Thêm Sân Mới
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                @if($arenas->count() > 0)
                    <div class="row g-4">
                        @foreach($arenas as $arena)
                            <div class="col-lg-6">
                                <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden h-100">
                                    {{-- Ảnh sân --}}
                                    @if($arena->image)
                                        <img src="{{ Storage::url($arena->image) }}"
                                             class="card-img-top"
                                             alt="{{ $arena->name }}"
                                             style="height: 200px; width: 100%; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="fas fa-image fa-3x text-muted opacity-25"></i>
                                            <span class="text-muted ms-2">Chưa có ảnh</span>
                                        </div>
                                    @endif

                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title fw-bold mb-1">{{ $arena->name }}</h5>
                                                <p class="text-muted small mb-0">
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($arena->location, 50) }}
                                                </p>
                                            </div>
                                            {{-- Status badge — dùng string comparison, không dùng truthy --}}
                                            @if($arena->isActive())
                                                <span class="badge bg-success text-white rounded-pill px-3 py-2">
                                                    <i class="fas fa-check-circle me-1"></i>Hoạt động
                                                </span>
                                            @elseif($arena->isMaintenance())
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                                    <i class="fas fa-wrench me-1"></i>Bảo trì
                                                </span>
                                            @else
                                                <span class="badge bg-secondary text-white rounded-pill px-3 py-2">
                                                    <i class="fas fa-eye-slash me-1"></i>Ẩn
                                                </span>
                                            @endif
                                        </div>

                                        @if($arena->maintenance_note && $arena->isMaintenance())
                                            <div class="alert alert-warning py-2 px-3 mb-3 rounded-3" style="font-size: .85rem;">
                                                <i class="fas fa-info-circle me-1"></i>{{ $arena->maintenance_note }}
                                            </div>
                                        @endif

                                        <div class="row g-3 mb-4">
                                            <div class="col-6">
                                                <p class="text-muted small mb-1">Loại Sân</p>
                                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $arena->type }}</span>
                                            </div>
                                            <div class="col-6">
                                                <p class="text-muted small mb-1">Giá</p>
                                                <h6 class="text-primary mb-0">{{ number_format($arena->price) }}đ/giờ</h6>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <a href="{{ route('owner.arenas.edit', $arena) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                                <i class="fas fa-edit me-1"></i> Sửa
                                            </a>

                                            <a href="{{ route('owner.reviews.index', ['arena_id' => $arena->id]) }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                                                <i class="fas fa-star me-1"></i> Xem Reviews
                                            </a>

                                            {{-- Nút bảo trì / kích hoạt --}}
                                            @if($arena->isMaintenance())
                                                <form action="{{ route('owner.arenas.toggle-maintenance', $arena) }}" method="POST" class="flex-grow-1">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm w-100"
                                                            onclick="return confirm('Kích hoạt lại sân {{ $arena->name }}?')">
                                                        <i class="fas fa-play me-1"></i> Kích hoạt
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-warning btn-sm flex-grow-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#maintenanceModal"
                                                        data-arena-id="{{ $arena->id }}"
                                                        data-arena-name="{{ $arena->name }}">
                                                    <i class="fas fa-wrench me-1"></i> Bảo trì
                                                </button>
                                            @endif

                                            <form action="{{ route('owner.arenas.destroy', $arena) }}" method="POST"
                                                  onsubmit="return confirm('Bạn chắc chắn muốn xóa sân này?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(method_exists($arenas, 'links'))
                        <div class="mt-5">{{ $arenas->links() }}</div>
                    @endif
                @else
                    <div class="card border-0 rounded-4 shadow-sm bg-white py-5">
                        <div class="text-center px-4">
                            <i class="fas fa-inbox fa-4x text-muted mb-4 opacity-25"></i>
                            <h4 class="mb-3">Bạn chưa có sân nào</h4>
                            <p class="text-muted mb-4">Hãy thêm sân bóng của bạn để bắt đầu nhận đơn đặt sân.</p>
                            <a href="{{ route('owner.arenas.create') }}" class="btn btn-primary btn-lg px-5 py-3">Thêm Sân Ngay</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Modal nhập lý do bảo trì --}}
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title"><i class="fas fa-wrench text-warning me-2"></i>Chuyển sang bảo trì</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="maintenanceForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body pt-3">
                    <p class="text-muted mb-3">Sân: <strong id="maintenanceArenaName"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Lý do bảo trì <span class="text-muted">(tùy chọn)</span></label>
                        <textarea name="maintenance_note" class="form-control" rows="3"
                                  placeholder="Ví dụ: Thay cỏ nhân tạo, sửa hệ thống đèn..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4">
                        <i class="fas fa-wrench me-1"></i> Xác nhận bảo trì
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const maintenanceModal = document.getElementById('maintenanceModal');
maintenanceModal.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const arenaId   = btn.dataset.arenaId;
    const arenaName = btn.dataset.arenaName;
    document.getElementById('maintenanceArenaName').textContent = arenaName;
    document.getElementById('maintenanceForm').action = '/owner/arenas/' + arenaId + '/toggle-maintenance';
});
</script>
@endpush
@endsection
