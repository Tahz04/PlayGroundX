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

                        @if($errors->any())
                            <div class="alert alert-danger mb-4" style="border-radius: 12px; border: none;">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ $errors->first() }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger mb-4" style="border-radius: 12px; border: none;">
                                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        <form id="booking-form" action="{{ route('bookings.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="arena_id" value="{{ $arena->id }}">

                            <div class="mb-4">
                                <label class="form-label fw-bold">1. Chọn Ngày Đặt</label>
                                <input type="date" name="date" class="form-control form-control-lg" min="{{ date('Y-m-d') }}" value="{{ old('date', date('Y-m-d')) }}" required>
                            </div>

                            <div class="mb-5">
                                <label class="form-label fw-bold mb-3">2. Chọn Khung Giờ</label>
                                <p class="text-muted small mb-3">Chọn giờ bắt đầu và giờ kết thúc, hệ thống sẽ tự đặt toàn bộ khung giờ liên tiếp trong khoảng đó.</p>
                                @if($timeSlots->isEmpty())
                                    <div class="alert alert-warning mb-0" style="border-radius: 12px; border: none;">
                                        <i class="fas fa-clock me-2"></i>
                                        Hiện chưa có khung giờ khả dụng. Vui lòng quay lại sau hoặc liên hệ quản trị viên.
                                    </div>
                                @else
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="start_hour" class="form-label fw-semibold">Giờ bắt đầu</label>
                                            <select class="form-select form-select-lg" id="start_hour" name="start_hour" required>
                                                <option value="">-- Chọn giờ bắt đầu --</option>
                                                @for($hour = 6; $hour <= 23; $hour++)
                                                    <option value="{{ $hour }}" {{ (int) old('start_hour', 6) === $hour ? 'selected' : '' }}>
                                                        {{ sprintf('%02d:00', $hour) }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_hour" class="form-label fw-semibold">Giờ kết thúc</label>
                                            <select class="form-select form-select-lg" id="end_hour" name="end_hour" required>
                                                <option value="">-- Chọn giờ kết thúc --</option>
                                                @for($hour = 7; $hour <= 24; $hour++)
                                                    <option value="{{ $hour }}" {{ (int) old('end_hour', 7) === $hour ? 'selected' : '' }}>
                                                        {{ sprintf('%02d:00', $hour) }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div id="booking-range-preview" class="text-muted small mt-2"></div>
                                    @error('start_hour')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                    @error('end_hour')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold mb-3">3. Phương Thức Thanh Toán</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input class="btn-check" type="radio" name="payment_method" id="payment-cash" value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-primary w-100 py-3 rounded-4 fw-semibold text-start" for="payment-cash">
                                            <i class="fas fa-money-bill-wave me-2"></i>Tiền mặt
                                            <span class="d-block small text-muted fw-normal mt-1">Thanh toán trực tiếp tại sân</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="btn-check" type="radio" name="payment_method" id="payment-transfer" value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-primary w-100 py-3 rounded-4 fw-semibold text-start" for="payment-transfer">
                                            <i class="fas fa-university me-2"></i>Chuyển khoản
                                            <span class="d-block small text-muted fw-normal mt-1">Chuyển sang trang thanh toán ngân hàng</span>
                                        </label>
                                    </div>
                                </div>
                                @error('payment_method')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-5">
                                <button id="confirm-booking-btn" type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-lg" style="border-radius: 15px;" {{ $timeSlots->isEmpty() ? 'disabled' : '' }}>
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
    .form-select-lg {
        padding: 0.8rem 1.25rem;
        border-radius: 15px;
        border: 2px solid #f1f5f9;
        font-weight: 600;
    }
    .form-control-lg:focus {
        border-color: var(--clr-primary-400);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }
    .form-select-lg:focus {
        border-color: var(--clr-primary-400);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('booking-form');
    const startSelect = document.getElementById('start_hour');
    const endSelect = document.getElementById('end_hour');
    const confirmBtn = document.getElementById('confirm-booking-btn');
    const preview = document.getElementById('booking-range-preview');
    const paymentOptions = document.querySelectorAll('input[name="payment_method"]');

    if (!form || !startSelect || !endSelect || !confirmBtn) {
        return;
    }

    const setButtonState = () => {
        const startHour = Number(startSelect.value);
        const endHour = Number(endSelect.value);
        const hasPaymentMethod = Array.from(paymentOptions).some(option => option.checked);

        if (!startSelect.value || !endSelect.value) {
            confirmBtn.disabled = true;
            if (preview) {
                preview.textContent = 'Vui lòng chọn đủ giờ bắt đầu và giờ kết thúc.';
                preview.classList.remove('text-danger');
            }
            return;
        }

        const isValid = endHour > startHour;

        confirmBtn.disabled = !isValid || !hasPaymentMethod;
        if (preview) {
            preview.textContent = isValid
                ? 'Bạn đang đặt từ ' + String(startHour).padStart(2, '0') + ':00 đến ' + String(endHour).padStart(2, '0') + ':00.'
                : 'Giờ kết thúc phải lớn hơn giờ bắt đầu (tối thiểu 1 tiếng).';
            preview.classList.toggle('text-danger', !isValid);
        }
    };

    const syncEndOptions = function () {
        const hasStart = startSelect.value !== '';
        const startHour = hasStart ? Number(startSelect.value) : 0;

        for (const option of endSelect.options) {
            if (!option.value) {
                option.disabled = false;
                continue;
            }

            const endHour = Number(option.value);
            option.disabled = hasStart && endHour <= startHour;
        }

        if (endSelect.selectedOptions.length > 0 && endSelect.selectedOptions[0].disabled) {
            endSelect.value = '';
        }
    };

    startSelect.addEventListener('change', function () {
        syncEndOptions();
        setButtonState();
    });

    endSelect.addEventListener('change', setButtonState);
    paymentOptions.forEach(option => option.addEventListener('change', setButtonState));

    form.addEventListener('submit', function () {
        if (confirmBtn.disabled) {
            return;
        }
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = 'Đang gửi yêu cầu...';
    });

    syncEndOptions();
    setButtonState();
});
</script>
@endsection
