@extends('layouts.app')

@section('title', 'Đặt Sân: ' . $arena->name)

@section('content')
<div class="container" style="margin-top: 120px; margin-bottom: 60px;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
                <div class="row g-0">
                    <!-- Left: Info -->
                    <div class="col-lg-5 bg-dark text-white p-5 d-flex flex-column justify-content-center" style="background: linear-gradient(rgba(15,23,42,0.9), rgba(15,23,42,0.9)), url('https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=600&fit=crop') center/cover;">
                        <div class="mb-4">
                            <span class="badge bg-primary mb-2">{{ $arena->type }}</span>
                            <h2 class="fw-bold display-6 mb-3">{{ $arena->name }}</h2>
                            <p class="text-white-50"><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $arena->location }}</p>
                        </div>
                        <div class="pricing-info mb-5">
                            <h3 class="fw-bold text-primary">{{ number_format($arena->price) }}đ <small class="text-white-50 fs-6">/ 90 phút</small></h3>
                        </div>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Sân cỏ nhân tạo đạt chuẩn</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Hệ thống đèn LED hiện đại</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i>Phòng thay đồ & Nước uống</li>
                        </ul>
                    </div>

                    <!-- Right: Form -->
                    <div class="col-lg-7 p-5 bg-white">
                        <h3 class="fw-bold mb-4">Chọn Thông Tin Đặt Sân</h3>
                        
                        @if(session('error'))
                            <div class="alert alert-danger mb-4" style="border-radius: 12px; border: none;">
                                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('bookings.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="arena_id" value="{{ $arena->id }}">

                            <div class="mb-4">
                                <label class="form-label fw-bold">1. Chọn Ngày Đặt</label>
                                <input type="date" name="date" class="form-control form-control-lg" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-5">
                                <label class="form-label fw-bold mb-3">2. Chọn Khung Giờ</label>
                                <div class="row g-3">
                                    @foreach($timeSlots as $slot)
                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check" name="time_slot_id" id="slot-{{ $slot->id }}" value="{{ $slot->id }}" required>
                                            <label class="btn btn-outline-primary w-100 py-3 rounded-4 fw-semibold" for="slot-{{ $slot->id }}">
                                                <i class="far fa-clock me-2"></i>{{ $slot->formattedTime() }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-5">
                                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-lg" style="border-radius: 15px;">
                                    Xác Nhận Đặt Sân <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                                <p class="text-center text-muted small mt-3">Nhấn xác nhận đồng nghĩa với việc bạn đồng ý với <a href="#">Điều khoản sử dụng</a> của chúng tôi.</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-outline-primary {
        border-width: 2px;
        color: var(--clr-dark-700);
        border-color: #e2e8f0;
    }
    .btn-outline-primary:hover, .btn-check:checked + .btn-outline-primary {
        background-color: var(--clr-primary-500);
        border-color: var(--clr-primary-500);
        color: white;
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.2);
    }
    .form-control-lg {
        padding: 0.8rem 1.25rem;
        border-radius: 15px;
        border: 2px solid #f1f5f9;
        font-weight: 600;
    }
    .form-control-lg:focus {
        border-color: var(--clr-primary-400);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }
</style>
@endsection
