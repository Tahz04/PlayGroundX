@extends('layouts.app')

@section('title', 'Đặt Sân: ' . $arena->name)

@section('content')
<div class="container" style="margin-top: 120px; margin-bottom: 60px;">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg" style="border-radius: 25px; overflow: hidden;">
                <div class="row g-0">
                    <!-- Left: Info -->
                    @php
                    $bgImage = $arena->image ? asset('storage/' . $arena->image) : null;
                    @endphp
                    <div class="col-lg-5 text-white p-5 d-flex flex-column justify-content-center" 
                    style="@if($bgImage) background: linear-gradient(rgba(15,23,42,0.85), rgba(15,23,42,0.85)), url('{{ $bgImage }}') center/cover; background-size: cover; @else background: linear-gradient(135deg, #1a1a2e 0%, #0f0f1a 100%); @endif">
                    <div class="mb-4">
                        <span class="badge bg-primary mb-2">{{ $arena->type }}</span>
                        <h2 class="fw-bold display-6 mb-3">{{ $arena->name }}</h2>
                        <p class="text-white-50"><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $arena->location }}</p>
                    </div>
                        <div class="pricing-info mb-5">
                            <h3 class="fw-bold text-primary" id="base-price-display">
                                {{ number_format($arena->price) }}đ 
                                <small class="text-white-50 fs-6">/ giờ</small>
                            </h3>
                            <div id="total-price-preview" class="mt-3 p-3 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-25" style="display: none;">
                                <div class="text-white-50 small mb-1">Tổng cộng tạm tính</div>
                                <div class="text-white fw-bold h4 mb-0" id="calculated-price">0đ</div>
                                <div class="text-primary-light small mt-1" id="calculated-duration">0 phút</div>
                            </div>
                            <div id="promo-notice" class="mt-2 p-2 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25" style="display: none;">
                                <div class="text-success small fw-bold">
                                    <i class="fas fa-percentage me-1"></i> Áp dụng ưu đãi giảm 10% cho trận đấu từ 3h!
                                </div>
                            </div>
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
                                                    @foreach(['00', '30'] as $min)
                                                        @php $time = sprintf('%02d:%s', $hour, $min); @endphp
                                                        <option value="{{ $time }}" data-slot-id="{{ $timeSlots->where('start_time', $time.':00')->first()?->id }}" {{ old('start_hour', '06:00') === $time ? 'selected' : '' }}>
                                                            {{ $time }}
                                                        </option>
                                                    @endforeach
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_hour" class="form-label fw-semibold">Giờ kết thúc</label>
                                            <select class="form-select form-select-lg" id="end_hour" name="end_hour" required>
                                                <option value="">-- Chọn giờ kết thúc --</option>
                                                @for($hour = 6; $hour <= 23; $hour++)
                                                    @foreach(['00', '30'] as $min)
                                                        @php 
                                                            $nextMin = (int)$min + 30;
                                                            $nextHour = $nextMin >= 60 ? $hour + 1 : $hour;
                                                            $nextMin = $nextMin >= 60 ? 0 : $nextMin;
                                                            $time = sprintf('%02d:%02d', $nextHour, $nextMin);
                                                        @endphp
                                                        <option value="{{ $time }}" data-slot-id="{{ $timeSlots->where('end_time', ($time === '24:00' ? '00:00:00' : $time.':00'))->first()?->id }}" {{ old('end_hour', '07:00') === $time ? 'selected' : '' }}>
                                                            {{ $time }}
                                                        </option>
                                                    @endforeach
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
    
    // Price preview elements
    const pricePreview = document.getElementById('total-price-preview');
    const calculatedPrice = document.getElementById('calculated-price');
    const calculatedDuration = document.getElementById('calculated-duration');

    const arenaPrice = {{ $arena->price }};
    const arenaDuration = 60; // 60 minutes for hourly pricing

    if (!form || !startSelect || !endSelect || !confirmBtn) {
        return;
    }

    const timeToMinutes = (timeStr) => {
        if (!timeStr) return 0;
        const [h, m] = timeStr.split(':').map(Number);
        return h * 60 + m;
    };

    const dateInput = document.getElementById('date');
    const bookedSlotIds = @json($bookedSlotIds);

    const updateBookedSlots = (bookedIds) => {
        // We need to disable options that correspond to booked slots
        // This is complex because a booking might block multiple 30-min slots
        for (const option of startSelect.options) {
            const slotId = parseInt(option.dataset.slotId);
            if (bookedIds.includes(slotId)) {
                option.disabled = true;
                option.classList.add('text-muted');
                option.innerHTML += ' (Hết)';
            } else {
                option.disabled = false;
                option.classList.remove('text-muted');
                option.innerHTML = option.value;
            }
        }
        
        // Similar for endSelect but end time represents the end of the slot
        // If 06:00-06:30 is booked, 06:30 is the earliest available start
        // And 06:30 is NOT a valid end for a NEW booking starting at 06:00
        syncEndOptions();
    };

    // Initialize with data from server
    updateBookedSlots(bookedSlotIds);

    if (dateInput) {
        dateInput.addEventListener('change', function() {
            const date = this.value;
            fetch(`{{ route('bookings.booked-slots', $arena) }}?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    updateBookedSlots(data.bookedSlotIds);
                    setButtonState();
                });
        });
    }

    const setButtonState = () => {
        const startVal = startSelect.value;
        const endVal = endSelect.value;
        
        const startMinutes = timeToMinutes(startVal);
        const endMinutes = timeToMinutes(endVal);
        
        const hasPaymentMethod = Array.from(paymentOptions).some(option => option.checked);

        if (!startVal || !endVal) {
            confirmBtn.disabled = true;
            if (preview) {
                preview.textContent = 'Vui lòng chọn đủ giờ bắt đầu và giờ kết thúc.';
                preview.classList.remove('text-danger');
            }
            pricePreview.style.display = 'none';
            return;
        }

        const duration = endMinutes - startMinutes;
        const isValid = duration >= 60; // Minimum 1 hour

        confirmBtn.disabled = !isValid || !hasPaymentMethod;
        
        if (isValid) {
            preview.textContent = 'Bạn đang đặt từ ' + startVal + ' đến ' + endVal + '.';
            preview.classList.remove('text-danger');

            // Calculate Price
            let totalPrice = (duration / arenaDuration) * arenaPrice;
            
            // Promotion Logic
            const promoNotice = document.getElementById('promo-notice');
            if (duration >= 180) {
                totalPrice = totalPrice * 0.9;
                promoNotice.style.display = 'block';
            } else {
                promoNotice.style.display = 'none';
            }

            calculatedPrice.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + 'đ';
            calculatedDuration.textContent = duration + ' phút (' + (duration/60).toFixed(1) + ' giờ)';
            pricePreview.style.display = 'block';
        } else {
            preview.textContent = duration <= 0 
                ? 'Giờ kết thúc phải lớn hơn giờ bắt đầu.' 
                : 'Thời gian đặt tối thiểu là 60 phút (hiện tại: ' + duration + ' phút).';
            preview.classList.add('text-danger');
            pricePreview.style.display = 'none';
        }
    };

    const syncEndOptions = function () {
        const startVal = startSelect.value;
        const startMinutes = timeToMinutes(startVal);

        for (const option of endSelect.options) {
            if (!option.value) continue;
            const endMinutes = timeToMinutes(option.value);
            option.disabled = startVal && endMinutes <= startMinutes;
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
