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
                                <input type="date" id="date" name="date" class="form-control form-control-lg" min="{{ date('Y-m-d') }}" value="{{ old('date', date('Y-m-d')) }}" required>
                            </div>

                            <div class="mb-5">
                                <label class="form-label fw-bold mb-3">2. Chọn Khung Giờ</label>
                                <p class="text-muted small mb-3">Chọn giờ bắt đầu và giờ kết thúc, hệ thống sẽ tự đặt toàn bộ khung giờ liên tiếp trong khoảng đó.</p>

                                {{-- Visual time grid --}}
                                <div class="mb-3 p-3 rounded-3" style="background:#f8fafc;border:1.5px solid #e2e8f0;">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <span class="fw-semibold small">Lịch trống:</span>
                                        <span class="d-flex align-items-center gap-1 small">
                                            <span style="display:inline-block;width:12px;height:12px;background:#dcfce7;border:1px solid #86efac;border-radius:3px;"></span> Còn trống
                                        </span>
                                        <span class="d-flex align-items-center gap-1 small">
                                            <span style="display:inline-block;width:12px;height:12px;background:#fee2e2;border:1px solid #fca5a5;border-radius:3px;"></span> Đã đặt
                                        </span>
                                    </div>
                                    <div id="time-grid" class="d-flex flex-wrap gap-1"></div>
                                </div>

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
                                                        <option value="{{ $time }}" {{ old('start_hour') === $time ? 'selected' : '' }}>
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
                                                        <option value="{{ $time }}" {{ old('end_hour') === $time ? 'selected' : '' }}>
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
    .form-control-lg, .form-select-lg {
        padding: 0.8rem 1.25rem;
        border-radius: 15px;
        border: 2px solid #f1f5f9;
        font-weight: 600;
    }
    .form-control-lg:focus, .form-select-lg:focus {
        border-color: var(--clr-primary-400);
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form         = document.getElementById('booking-form');
    const startSelect  = document.getElementById('start_hour');
    const endSelect    = document.getElementById('end_hour');
    const confirmBtn   = document.getElementById('confirm-booking-btn');
    const preview      = document.getElementById('booking-range-preview');
    const dateInput    = document.getElementById('date');
    const paymentOpts  = document.querySelectorAll('input[name="payment_method"]');
    const pricePreview = document.getElementById('total-price-preview');
    const calcPrice    = document.getElementById('calculated-price');
    const calcDur      = document.getElementById('calculated-duration');
    const promoNotice  = document.getElementById('promo-notice');
    const arenaPrice   = {{ $arena->price }};

    if (!form || !startSelect || !endSelect || !confirmBtn) return;

    // ── Helpers ───────────────────────────────────────────────────────────────
    const toMin = t => {
        if (!t) return 0;
        const parts = t.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    };

    // Two ranges overlap when they share any minute (exclusive end)
    const rangesOverlap = (s1, e1, s2, e2) =>
        !(toMin(e1) <= toMin(s2) || toMin(s1) >= toMin(e2));

    const slotBooked = (slotStart, slotEnd) =>
        bookedRanges.some(r => rangesOverlap(slotStart, slotEnd, r.start, r.end));

    // ── Booked-ranges state (PHP → JS, already HH:MM normalized) ─────────────
    let bookedRanges = @json($bookedRanges);

    // ── Time grid ─────────────────────────────────────────────────────────────
    const buildGrid = () => {
        const grid = document.getElementById('time-grid');
        if (!grid) return;
        grid.innerHTML = '';
        for (let h = 6; h < 24; h++) {
            for (let m = 0; m < 60; m += 30) {
                const sH = String(h).padStart(2, '0');
                const sM = String(m).padStart(2, '0');
                const em = h * 60 + m + 30;
                const eH = String(Math.floor(em / 60)).padStart(2, '0');
                const eM = String(em % 60).padStart(2, '0');
                const s  = `${sH}:${sM}`;
                const e  = em >= 1440 ? '24:00' : `${eH}:${eM}`;

                const booked = slotBooked(s, e);
                const cell = document.createElement('div');
                cell.style.cssText =
                    'padding:3px 8px;border-radius:6px;font-size:11px;font-weight:700;line-height:1.7;cursor:default;' +
                    (booked
                        ? 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;text-decoration:line-through;opacity:.85;'
                        : 'background:#dcfce7;color:#166534;border:1px solid #86efac;');
                cell.title       = `${s}–${e}: ${booked ? 'Đã có người đặt' : 'Còn trống'}`;
                cell.textContent = s;
                grid.appendChild(cell);
            }
        }
    };

    // ── Dropdown options ──────────────────────────────────────────────────────
    const refreshStartOpts = () => {
        for (const opt of startSelect.options) {
            if (!opt.value) continue;
            // A start time is blocked if there is ANY booked range that
            // covers at least the first 30-min slot starting here.
            const slotEnd = (() => {
                const em = toMin(opt.value) + 30;
                return `${String(Math.floor(em/60)).padStart(2,'0')}:${String(em%60).padStart(2,'0')}`;
            })();
            const blocked = slotBooked(opt.value, slotEnd);
            opt.disabled     = blocked;
            opt.textContent  = blocked ? `${opt.value} (Đã đặt)` : opt.value;
        }
    };

    const refreshEndOpts = () => {
        const sv = startSelect.value;
        for (const opt of endSelect.options) {
            if (!opt.value) continue;
            // Disable if: no start chosen, end ≤ start, or range conflicts
            opt.disabled = !sv
                || toMin(opt.value) <= toMin(sv)
                || slotBooked(sv, opt.value);
            opt.textContent = opt.value; // reset label
        }
        // Clear selected end if it became disabled
        if (endSelect.value && endSelect.options[endSelect.selectedIndex]?.disabled) {
            endSelect.value = '';
        }
    };

    // ── Price preview ─────────────────────────────────────────────────────────
    const refreshUI = () => {
        const sv = startSelect.value;
        const ev = endSelect.value;
        const hasPay = Array.from(paymentOpts).some(o => o.checked);

        if (!sv || !ev) {
            confirmBtn.disabled = true;
            if (preview) preview.textContent = '';
            pricePreview.style.display = 'none';
            return;
        }

        const dur = toMin(ev) - toMin(sv);

        if (dur < 60) {
            confirmBtn.disabled = true;
            if (preview) {
                preview.textContent = dur <= 0
                    ? 'Giờ kết thúc phải lớn hơn giờ bắt đầu.'
                    : `Tối thiểu 60 phút (đang chọn ${dur} phút).`;
                preview.className = 'text-danger small mt-2';
            }
            pricePreview.style.display = 'none';
            return;
        }

        confirmBtn.disabled = !hasPay;
        if (preview) {
            preview.textContent = `Bạn đang đặt từ ${sv} đến ${ev}.`;
            preview.className   = 'text-muted small mt-2';
        }

        let price = (dur / 60) * arenaPrice;
        if (dur >= 180) {
            price *= 0.9;
            promoNotice.style.display = 'block';
        } else {
            promoNotice.style.display = 'none';
        }
        calcPrice.textContent = new Intl.NumberFormat('vi-VN').format(Math.round(price)) + 'đ';
        calcDur.textContent   = `${dur} phút (${(dur / 60).toFixed(1)} giờ)`;
        pricePreview.style.display = 'block';
    };

    // ── Date change → reload booked slots via AJAX ────────────────────────────
    if (dateInput) {
        dateInput.addEventListener('change', function () {
            fetch(`{{ route('bookings.booked-slots', $arena) }}?date=${this.value}`)
                .then(r => r.json())
                .then(data => {
                    bookedRanges = Array.isArray(data.bookedRanges) ? data.bookedRanges : [];
                    buildGrid();
                    refreshStartOpts();
                    refreshEndOpts();
                    refreshUI();
                });
        });
    }

    // ── Event listeners ───────────────────────────────────────────────────────
    startSelect.addEventListener('change', () => { refreshEndOpts(); refreshUI(); });
    endSelect.addEventListener('change', refreshUI);
    paymentOpts.forEach(o => o.addEventListener('change', refreshUI));
    form.addEventListener('submit', () => {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi yêu cầu...';
    });

    // ── Initial render ────────────────────────────────────────────────────────
    buildGrid();
    refreshStartOpts();
    refreshEndOpts();
    refreshUI();
});
</script>
@endsection
