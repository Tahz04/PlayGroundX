@extends('layouts.app')

@section('title', 'Quản Lý Sân Bóng Đá - Admin')

@section('content')
<div class="container" style="margin-top: 100px; margin-bottom: 50px;">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fas fa-tasks text-primary me-2"></i>Quản Lý Sân Bóng Đá</h2>
            <p class="text-muted mb-0">Danh sách tất cả các sân trên hệ thống</p>
        </div>
        <a href="{{ route('admin.arenas.create') }}" class="btn btn-primary px-4 py-2 rounded-pill">
            <i class="fas fa-plus me-2"></i>Thêm Sân Mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(16,185,129,0.15);">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search & Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.arenas.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    {{-- Search --}}
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Tìm kiếm</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted" style="font-size:.85rem;"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                   placeholder="Tên sân hoặc địa chỉ..."
                                   value="{{ request('search') }}" autocomplete="off">
                        </div>
                    </div>

                    {{-- Loại sân --}}
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Loại sân</label>
                        <select name="type" class="form-select">
                            <option value="">Tất cả loại</option>
                            <option value="Sân 5"  {{ request('type') == 'Sân 5'  ? 'selected' : '' }}>Sân 5 người</option>
                            <option value="Sân 7"  {{ request('type') == 'Sân 7'  ? 'selected' : '' }}>Sân 7 người</option>
                            <option value="Sân 11" {{ request('type') == 'Sân 11' ? 'selected' : '' }}>Sân 11 người</option>
                        </select>
                    </div>

                    {{-- Trạng thái --}}
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="active"      {{ request('status') == 'active'      ? 'selected' : '' }}>Hoạt động</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                            <option value="inactive"    {{ request('status') == 'inactive'    ? 'selected' : '' }}>Tạm ngưng</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="d-flex gap-2 mt-1">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-filter me-1"></i>Lọc
                            </button>
                            @if(request()->hasAny(['search', 'type', 'status']))
                                <a href="{{ route('admin.arenas.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Xóa lọc
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Active filter chips --}}
                @if(request()->hasAny(['search', 'type', 'status']))
                    <div class="d-flex align-items-center gap-2 flex-wrap mt-3">
                        <span class="text-muted small">Đang lọc:</span>
                        @if(request('search'))
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1">
                                <i class="fas fa-search me-1"></i>{{ request('search') }}
                            </span>
                        @endif
                        @if(request('type'))
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-1">
                                <i class="fas fa-futbol me-1"></i>{{ request('type') }}
                            </span>
                        @endif
                        @if(request('status'))
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1">
                                <i class="fas fa-circle me-1"></i>{{ ['active'=>'Hoạt động','maintenance'=>'Bảo trì','inactive'=>'Tạm ngưng'][request('status')] ?? request('status') }}
                            </span>
                        @endif
                        <span class="text-muted small ms-1">— {{ $arenas->total() }} kết quả</span>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Ảnh</th>
                        <th>Tên Sân</th>
                        <th>Địa Chỉ</th>
                        <th>Giá/Giờ</th>
                        <th>Trạng Thái</th>
                        <th>Tọa Độ</th>
                        <th class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($arenas as $arena)
                        <tr>
                            <td class="ps-4" style="width:80px;">
                                @if($arena->image)
                                    <img src="{{ Storage::url($arena->image) }}" alt="{{ $arena->name }}"
                                         style="width:60px;height:50px;object-fit:cover;border-radius:8px;">
                                @else
                                    <div style="width:60px;height:50px;background:#e9ecef;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $arena->name }}</div>
                                <div class="small text-primary fw-semibold">{{ $arena->type }}</div>
                            </td>
                            <td>
                                <i class="fas fa-map-marker-alt text-danger me-1 small"></i>
                                <span class="text-muted small">{{ Str::limit($arena->location, 45) }}</span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ number_format($arena->price) }}đ</span>
                            </td>
                            <td>
                                @if($arena->status === 'active')
                                    <span class="badge rounded-pill px-3 py-2" style="background:rgba(16,185,129,.12);color:#059669;border:1px solid rgba(16,185,129,.25);">
                                        <i class="fas fa-check-circle me-1"></i>Hoạt động
                                    </span>
                                @elseif($arena->status === 'maintenance')
                                    <span class="badge rounded-pill px-3 py-2" style="background:rgba(245,158,11,.12);color:#d97706;border:1px solid rgba(245,158,11,.25);">
                                        <i class="fas fa-wrench me-1"></i>Bảo trì
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2" style="background:rgba(100,116,139,.12);color:#64748b;border:1px solid rgba(100,116,139,.25);">
                                        <i class="fas fa-times-circle me-1"></i>Tạm ngưng
                                    </span>
                                @endif
                            </td>
                            <td>
                                <code class="small text-muted">{{ $arena->latitude }}, {{ $arena->longitude }}</code>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.arenas.edit', $arena) }}"
                                       class="btn btn-sm btn-outline-info" style="border-radius:8px;" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.arenas.destroy', $arena) }}" method="POST"
                                          onsubmit="return confirm('Xóa sân {{ addslashes($arena->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                style="border-radius:8px;" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-search fa-2x text-muted mb-3 d-block"></i>
                                <span class="text-muted">
                                    @if(request()->hasAny(['search','type','status']))
                                        Không tìm thấy sân phù hợp. <a href="{{ route('admin.arenas.index') }}">Xóa bộ lọc</a>
                                    @else
                                        Chưa có sân nào được thêm.
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($arenas->hasPages())
        <div class="mt-4">{{ $arenas->links() }}</div>
    @endif
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
.form-select, .form-control {
    height: 42px;
    border-color: #e2e8f0;
    font-size: .9rem;
}
.form-select:focus, .form-control:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16,185,129,.12);
}
.input-group-text { border-color: #e2e8f0; }
</style>
@endsection
