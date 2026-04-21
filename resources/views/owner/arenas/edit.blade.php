@extends('layouts.app')

@section('title', 'Quản Lý Sân - PlayGroundX')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5 align-items-center">
            <div class="col">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill py-2 px-3 mb-3">Quản Lý Sân</span>
                <h1 class="display-6 mb-3">Sửa Sân</h1>
                <p class="text-muted">Cập nhật thông tin sân của bạn.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm bg-white">
                    <div class="card-body p-5">
                        <form action="{{ route('owner.arenas.update', $arena) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold">Tên Sân <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="VD: Sân Bóng ABC" value="{{ old('name', $arena->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                             <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="type" class="form-label fw-bold">Loại Sân <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                                        <option value="">-- Chọn loại sân --</option>
                                        <option value="Sân 5" {{ old('type', $arena->type) === 'Sân 5' ? 'selected' : '' }}>Sân 5</option>
                                        <option value="Sân 7" {{ old('type', $arena->type) === 'Sân 7' ? 'selected' : '' }}>Sân 7</option>
                                        <option value="Sân 11" {{ old('type', $arena->type) === 'Sân 11' ? 'selected' : '' }}>Sân 11</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="price" class="form-label fw-bold">Giá (đ) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" placeholder="500000" value="{{ old('price', $arena->price) }}" step="1000">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="location" class="form-label fw-bold">Địa Điểm <span class="text-danger">*</span></label>
                                <input type="text" name="location" id="location" class="form-control @error('location') is-invalid @enderror" placeholder="VD: 123 Đường ABC, Quận 1, TP HCM" value="{{ old('location', $arena->location) }}">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="latitude" class="form-label fw-bold">Vĩ độ <span class="text-danger">*</span></label>
                                    <input type="number" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" placeholder="10.7769" value="{{ old('latitude', $arena->latitude) }}" step="0.0001">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="longitude" class="form-label fw-bold">Kinh độ <span class="text-danger">*</span></label>
                                    <input type="number" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" placeholder="106.7009" value="{{ old('longitude', $arena->longitude) }}" step="0.0001">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-hero-primary btn-lg">
                                    <i class="fas fa-save me-2"></i> Lưu Thay Đổi
                                </button>
                                <a href="{{ route('owner.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i> Quay Lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
