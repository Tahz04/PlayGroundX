@extends('layouts.app')

@section('title', 'Thêm Sân Mới - Admin')

@section('content')
<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.arenas.index') }}" class="btn btn-link text-decoration-none text-muted me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="fw-bold mb-0">Thêm Sân Bóng Đá Mới</h2>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4 p-md-5">
                    {{-- ⚠️ THÊM enctype="multipart/form-data" --}}
                    <form action="{{ route('admin.arenas.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Tên Sân</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="VD: Sân Bóng Đá Phú Mỹ Hưng" value="{{ old('name') }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Loại Sân</label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="Sân 5" {{ old('type') == 'Sân 5' ? 'selected' : '' }}>Sân 5 Người</option>
                                    <option value="Sân 7" {{ old('type') == 'Sân 7' ? 'selected' : '' }}>Sân 7 Người</option>
                                    <option value="Sân 11" {{ old('type') == 'Sân 11' ? 'selected' : '' }}>Sân 11 Người</option>
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Địa Chỉ</label>
                                <textarea name="location" class="form-control @error('location') is-invalid @enderror" rows="2" placeholder="Nhập địa chỉ chi tiết..." required>{{ old('location') }}</textarea>
                                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Giá thuê/giờ (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" placeholder="80000" value="{{ old('price') }}" required>
                                    <span class="input-group-text">đ</span>
                                </div>
                                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- 🖼️ THÊM PHẦN UPLOAD ẢNH --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Ảnh Đại Diện Sân (Avt)</label>
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Hỗ trợ: JPEG, PNG, JPG, GIF (tối đa 2MB)</small>
                                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ảnh Phụ 1 (Tùy chọn)</label>
                                <input type="file" name="image_1" class="form-control @error('image_1') is-invalid @enderror" accept="image/*">
                                @error('image_1') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ảnh Phụ 2 (Tùy chọn)</label>
                                <input type="file" name="image_2" class="form-control @error('image_2') is-invalid @enderror" accept="image/*">
                                @error('image_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-3 opacity-10">
                                <h5 class="fw-bold mb-3"><i class="fas fa-map-marker-alt text-primary me-2"></i>Tọa độ bản đồ</h5>
                                <p class="text-muted small mb-4">Bạn có thể lấy tọa độ từ Google Maps (chuột phải vào bản đồ chọn tọa độ)</p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vĩ độ (Latitude)</label>
                                <input type="text" name="latitude" id="lat" class="form-control @error('latitude') is-invalid @enderror" placeholder="10.776643" value="{{ old('latitude') }}" required>
                                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kinh độ (Longitude)</label>
                                <input type="text" name="longitude" id="lng" class="form-control @error('longitude') is-invalid @enderror" placeholder="106.671542" value="{{ old('longitude') }}" required>
                                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12 mt-5">
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" style="border-radius: 12px; font-size: 1.1rem;">
                                    <i class="fas fa-save me-2"></i>Lưu Thông Tin Sân
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