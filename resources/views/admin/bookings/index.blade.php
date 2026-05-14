@extends('layouts.app')

@section('title', 'Quản Lý Đơn Đặt Sân - Admin')

@section('content')
<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fas fa-clipboard-list text-primary me-2"></i>Quản Lý Đơn Đặt Sân</h2>
            <p class="text-muted mb-0">Xem và xử lý các yêu cầu đặt sân từ người dùng</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(16,185,129,0.15);">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Search & Filter Bar --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.bookings.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    {{-- Search --}}
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Tìm kiếm</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted" style="font-size:.85rem;"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0"
                                   placeholder="Tên khách, email hoặc tên sân..."
                                   value="{{ request('search') }}" autocomplete="off">
                        </div>
                    </div>

                    {{-- Trạng thái --}}
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>

                    {{-- Ngày --}}
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Ngày đặt</label>
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>

                    {{-- Buttons --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="d-flex gap-2 mt-1">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-filter me-1"></i>Lọc
                            </button>
                            @if(request()->hasAny(['search', 'status', 'date']))
                                <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Xóa lọc
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Active filter chips --}}
                @if(request()->hasAny(['search', 'status', 'date']))
                    <div class="d-flex align-items-center gap-2 flex-wrap mt-3">
                        <span class="text-muted small">Đang lọc:</span>
                        @if(request('search'))
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1">
                                <i class="fas fa-search me-1"></i>{{ request('search') }}
                            </span>
                        @endif
                        @if(request('status'))
                            @php
                                $statusLabels = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','completed'=>'Hoàn thành','cancelled'=>'Đã hủy'];
                            @endphp
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1">
                                <i class="fas fa-circle me-1"></i>{{ $statusLabels[request('status')] ?? request('status') }}
                            </span>
                        @endif
                        @if(request('date'))
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-1">
                                <i class="far fa-calendar-alt me-1"></i>{{ date('d/m/Y', strtotime(request('date'))) }}
                            </span>
                        @endif
                        <span class="text-muted small ms-1">— {{ $bookings->total() }} kết quả</span>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Khách Hàng</th>
                        <th>Sân Bóng</th>
                        <th>Thời Gian</th>
                        <th>Trạng Thái</th>
                        <th class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $booking->user->name }}</div>
                                <div class="small text-muted">{{ $booking->user->email }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $booking->arena->name }}</div>
                                <div class="small text-primary fw-semibold">{{ $booking->arena->type }}</div>
                            </td>
                            <td>
                                <div><i class="far fa-calendar-alt me-1"></i>{{ date('d/m/Y', strtotime($booking->date)) }}</div>
                                <div class="fw-bold text-primary small"><i class="far fa-clock me-1"></i>
                                    @php
                                        $timeDisplay = 'N/A';
                                        $hasNewFormat = isset($booking->start_time, $booking->end_time) && 
                                                       !is_null($booking->start_time) && 
                                                       !is_null($booking->end_time) && 
                                                       $booking->start_time !== '' && 
                                                       $booking->end_time !== '';
                                        
                                        if ($hasNewFormat) {
                                            try {
                                                $start = \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->format('H:i');
                                                $end = \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)->format('H:i');
                                                $timeDisplay = $start . ' - ' . $end;
                                            } catch (\Exception $e) {
                                                $timeDisplay = 'Error';
                                            }
                                        } elseif ($booking->timeSlot) {
                                            $timeDisplay = $booking->timeSlot->formattedTime();
                                        }
                                    @endphp
                                    {{ $timeDisplay }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = [
                                        'pending' => 'bg-warning text-dark',
                                        'confirmed' => 'bg-success text-white',
                                        'cancelled' => 'bg-danger text-white',
                                        'completed' => 'bg-info text-white'
                                    ][$booking->status] ?? 'bg-secondary text-white';
                                @endphp
                                @php
                                    $statusLabel = ['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','cancelled'=>'Đã hủy','completed'=>'Hoàn thành'][$booking->status] ?? $booking->status;
                                @endphp
                                <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 small">{{ $statusLabel }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST" class="d-inline-block">
                                        @csrf @method('PATCH')
                                        <div class="input-group input-group-sm">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="pending"   {{ $booking->status == 'pending'   ? 'selected' : '' }}>Chờ</option>
                                                <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Xác nhận</option>
                                                <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Xong</option>
                                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Hủy</option>
                                            </select>
                                        </div>
                                    </form>

                                    {{-- Timer: chỉ hiện cho đơn đã confirmed hôm nay --}}
                                    @if(in_array($booking->status, ['confirmed','paid']) && $booking->date === now()->toDateString() && $booking->end_time)
                                        @if($booking->timer_started_at)
                                            <div class="countdown-timer text-success fw-bold small d-flex align-items-center gap-1"
                                                 data-end-datetime="{{ $booking->date }}T{{ $booking->end_time }}">
                                                <i class="fas fa-stopwatch"></i>
                                                <span class="timer-display">—</span>
                                            </div>
                                        @else
                                            <button class="btn btn-sm btn-outline-info rounded-pill px-3 start-timer-btn"
                                                    data-timer-url="{{ route('admin.bookings.start-timer', $booking) }}">
                                                <i class="fas fa-play me-1"></i> Bắt đầu timer
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-clipboard-list fa-2x text-muted mb-3 d-block"></i>
                                <span class="text-muted">
                                    @if(request()->hasAny(['search','status','date']))
                                        Không tìm thấy đơn đặt sân phù hợp. <a href="{{ route('admin.bookings.index') }}">Xóa bộ lọc</a>
                                    @else
                                        Chưa có đơn đặt sân nào.
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($bookings->hasPages())
        <div class="mt-4">{{ $bookings->links() }}</div>
    @endif
</div>

@push('scripts')
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    function startCountdown(displayEl, endDateTimeStr) {
        function tick() {
            const diff = Math.floor((new Date(endDateTimeStr) - new Date()) / 1000);
            if (diff <= 0) {
                displayEl.textContent = 'Hết giờ';
                displayEl.closest('.countdown-timer').classList.replace('text-success', 'text-danger');
                return;
            }
            const h = Math.floor(diff / 3600);
            const m = Math.floor((diff % 3600) / 60);
            const s = diff % 60;
            displayEl.textContent = (h ? h + ':' : '') + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
            if (diff <= 900) {
                displayEl.closest('.countdown-timer').classList.replace('text-success', 'text-warning');
            }
            setTimeout(tick, 1000);
        }
        tick();
    }

    document.querySelectorAll('.countdown-timer').forEach(function (el) {
        startCountdown(el.querySelector('.timer-display'), el.dataset.endDatetime);
    });

    document.querySelectorAll('.start-timer-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>';
            fetch(btn.dataset.timerUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) { alert(data.error); btn.disabled = false; btn.innerHTML = '<i class="fas fa-play me-1"></i> Bắt đầu timer'; return; }
                const wrapper = document.createElement('div');
                wrapper.className = 'countdown-timer text-success fw-bold small d-flex align-items-center gap-1';
                wrapper.dataset.endDatetime = data.end_time_datetime;
                wrapper.innerHTML = '<i class="fas fa-stopwatch"></i><span class="timer-display">—</span>';
                btn.replaceWith(wrapper);
                startCountdown(wrapper.querySelector('.timer-display'), data.end_time_datetime);
            })
            .catch(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-play me-1"></i> Bắt đầu timer'; });
        });
    });
})();
</script>
@endpush

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
