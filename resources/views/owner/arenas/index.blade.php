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

        <div class="row">
            <div class="col-12">
                @if($arenas->count() > 0)
                    <div class="row g-4">
                        @foreach($arenas as $arena)
                            <div class="col-lg-6">
                                <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden h-100">
                                    {{-- 🖼️ HIỂN THỊ ẢNH --}}
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
                                    @endif  {{-- ⚠️ QUAN TRỌNG: PHẢI CÓ DÒNG NÀY --}}
                                    
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title fw-bold mb-1">{{ $arena->name }}</h5>
                                                <p class="text-muted small mb-0">
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($arena->location, 50) }}
                                                </p>
                                            </div>
                                            <span class="badge {{ $arena->status ? 'bg-success' : 'bg-secondary' }} text-white rounded-pill px-3 py-2">
                                                {{ $arena->status ? 'Hoạt động' : 'Ẩn' }}
                                            </span>
                                        </div>

                                        <div class="row g-3 mb-4">
                                            <div class="col-6">
                                                <p class="text-muted small mb-1">Loại Sân</p>
                                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $arena->type }}</span>
                                            </div>
                                            <div class="col-6">
                                                <p class="text-muted small mb-1">Giá</p>
                                                <h6 class="text-primary">{{ number_format($arena->price) }}đ</h6>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <a href="{{ route('owner.arenas.edit', $arena) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                                <i class="fas fa-edit me-1"></i> Sửa
                                            </a>
                                            <form action="{{ route('owner.arenas.destroy', $arena) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('Bạn chắc chắn muốn xóa sân này?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                    <i class="fas fa-trash me-1"></i> Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(method_exists($arenas, 'links'))
                        <div class="mt-5">
                            {{ $arenas->links() }}
                        </div>
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
@endsection