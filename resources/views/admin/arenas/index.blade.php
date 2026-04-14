@extends('layouts.app')

@section('title', 'Quản Lý Sân Cầu Lông - Admin')

@section('content')
<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="fas fa-tasks text-primary me-2"></i>Quản Lý Sân Bóng Đá</h2>
            <p class="text-muted">Danh sách tất cả các sân trên hệ thống</p>
        </div>
        <a href="{{ route('admin.arenas.create') }}" class="btn btn-primary px-4 py-2" style="border-radius: 50px;">
            <i class="fas fa-plus me-2"></i>Thêm Sân Mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(16,185,129,0.1);">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Tên Sân</th>
                        <th>Địa Chỉ</th>
                        <th>Giá/Giờ</th>
                        <th>Tọa Độ (Lat, Lng)</th>
                        <th class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($arenas as $arena)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold">{{ $arena->name }}</span>
                                <div class="small text-primary fw-semibold">{{ $arena->type }}</div>
                            </td>
                            <td>
                                <i class="fas fa-map-marker-alt text-danger me-1 small"></i>
                                <span class="text-muted">{{ $arena->location }}</span>
                            </td>
                            <td>
                                <span class="badge bg-soft-primary text-primary fw-bold" style="font-size: 0.9rem;">
                                    {{ number_format($arena->price) }}đ
                                </span>
                            </td>
                            <td>
                                <code class="small">{{ $arena->latitude }}, {{ $arena->longitude }}</code>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.arenas.edit', $arena) }}" class="btn btn-sm btn-outline-info" style="border-radius: 8px;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.arenas.destroy', $arena) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sân này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 8px;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-info-circle mb-2 fa-2x"></i>
                                    <p>Chưa có sân nào được thêm.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary {
        background-color: rgba(59, 130, 246, 0.1);
    }
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
        padding-top: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 2px solid #f1f5f9;
    }
    .table tbody td {
        padding-top: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }
</style>
@endsection
