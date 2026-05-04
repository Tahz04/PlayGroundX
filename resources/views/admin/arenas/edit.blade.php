@extends('layouts.app')

@section('title', 'Chỉnh Sửa Sân - Admin')

@section('content')
<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.arenas.index') }}" class="btn btn-link text-decoration-none text-muted me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">Chỉnh Sửa Sân: {{ $arena->name }}</h2>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4 p-md-5">
                    {{-- ⚠️ THÊM enctype="multipart/form-data" --}}
                    <form action="{{ route('admin.arenas.update', $arena) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Tên Sân</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="VD: Sân Bóng Đá Phú Mỹ Hưng" value="{{ old('name', $arena->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Loại Sân</label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="Sân 5" {{ old('type', $arena->type) == 'Sân 5' ? 'selected' : '' }}>Sân 5 Người</option>
                                    <option value="Sân 7" {{ old('type', $arena->type) == 'Sân 7' ? 'selected' : '' }}>Sân 7 Người</option>
                                    <option value="Sân 11" {{ old('type', $arena->type) == 'Sân 11' ? 'selected' : '' }}>Sân 11 Người</option>
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Địa Chỉ</label>
                                <textarea name="location" class="form-control @error('location') is-invalid @enderror" rows="2" placeholder="Nhập địa chỉ chi tiết..." required>{{ old('location', $arena->location) }}</textarea>
                                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Giá thuê/giờ (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" placeholder="80000" value="{{ old('price', $arena->price) }}" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Trạng Thái</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active"      {{ old('status', $arena->status) === 'active'      ? 'selected' : '' }}>✅ Đang hoạt động</option>
                                    <option value="maintenance" {{ old('status', $arena->status) === 'maintenance' ? 'selected' : '' }}>🔧 Đang bảo trì</option>
                                    <option value="inactive"    {{ old('status', $arena->status) === 'inactive'    ? 'selected' : '' }}>❌ Tạm ngưng</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">Bảo trì: sân vẫn hiển thị nhưng không thể đặt.</small>
                            </div>

                            {{-- 🖼️ HIỂN THỊ ẢNH HIỆN TẠI VÀ ĐỔI ẢNH --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Ảnh Đại Diện Sân (Avt)</label>
                                @if($arena->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($arena->image) }}" alt="{{ $arena->name }}" class="rounded shadow-sm" style="width: 150px; height: 100px; object-fit: cover;">
                                </div>
                                @endif
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Để trống nếu không muốn đổi ảnh. (tối đa 2MB)</small>
                                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ảnh Phụ 1</label>
                                @if($arena->image_1)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($arena->image_1) }}" alt="Ảnh phụ 1" class="rounded shadow-sm" style="width: 100%; height: 100px; object-fit: cover;">
                                </div>
                                @endif
                                <input type="file" name="image_1" class="form-control @error('image_1') is-invalid @enderror" accept="image/*">
                                @error('image_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ảnh Phụ 2</label>
                                @if($arena->image_2)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($arena->image_2) }}" alt="Ảnh phụ 2" class="rounded shadow-sm" style="width: 100%; height: 100px; object-fit: cover;">
                                </div>
                                @endif
                                <input type="file" name="image_2" class="form-control @error('image_2') is-invalid @enderror" accept="image/*">
                                @error('image_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-3 opacity-10">
                                <h5 class="fw-bold mb-3"><i class="fas fa-map-marker-alt text-primary me-2"></i>Tọa độ bản đồ</h5>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vĩ độ (Latitude)</label>
                                <input type="text" name="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $arena->latitude) }}" required>
                                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kinh độ (Longitude)</label>
                                <input type="text" name="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $arena->longitude) }}" required>
                                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 mt-5">
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" style="border-radius: 12px; font-size: 1.1rem;">
                                    <i class="fas fa-save me-2"></i>Cập Nhật Thông Tin
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control {
        padding: 0.75rem 1rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .form-control:focus {
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        border-color: #3b82f6;
    }
    .input-group-text {
        border-radius: 0 10px 10px 0;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-left: none;
    }
</style>
@endsection