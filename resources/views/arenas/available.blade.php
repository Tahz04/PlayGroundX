@extends('layouts.app')

@section('title', 'Lịch Trống Sân Bóng - PlayGroundX')

@section('content')

<!-- Hero -->
<section class="avail-hero">
    <div class="avail-hero-glow"></div>
    <div class="container position-relative" style="z-index:2">
        <div class="text-center" data-aos="fade-up">
            <div class="avail-hero-badge mb-3">
                <i class="fas fa-calendar-check me-2"></i>Lịch Trống Thời Gian Thực
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">
                Xem <span class="accent">Lịch Trống</span> Sân Bóng
            </h1>
            <p class="text-muted fs-5 mx-auto" style="max-width:640px">
                Chọn ngày để xem ngay các khung giờ còn trống của tất cả sân — đặt sân nhanh chỉ một click.
            </p>
        </div>
    </div>
</section>

<!-- Sticky Filter -->
<div class="avail-filter-bar">
    <div class="container">
        <form action="{{ route('arenas.available') }}" method="GET" id="filterForm">
            <div class="row g-2 align-items-center">

                <!-- Date picker -->
                <div class="col-lg-3 col-md-6">
                    <div class="input-group avail-input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-alt text-primary"></i></span>
                        <input type="date" name="date" class="form-control fw-semibold"
                               value="{{ $date }}"
                               min="{{ now()->toDateString() }}"
                               onchange="this.form.submit()">
                    </div>
                </div>

                <!-- Tìm tên sân -->
                <div class="col-lg-4 col-md-6">
                    <div class="input-group avail-input-group">
                        <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Tìm tên sân..."
                               value="{{ request('search') }}"
                               autocomplete="off">
                        @if(request('search'))
                            <button type="button" class="btn btn-outline-secondary border-start-0"
                                    onclick="document.querySelector('[name=search]').value='';this.form.submit()">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Loại sân -->
                <div class="col-lg-3 col-md-6">
                    <select name="type" class="form-select avail-select" onchange="this.form.submit()">
                        <option value="">Tất cả loại sân</option>
                        <option value="Sân 5" {{ request('type') == 'Sân 5' ? 'selected' : '' }}>Sân 5 Người</option>
                        <option value="Sân 7" {{ request('type') == 'Sân 7' ? 'selected' : '' }}>Sân 7 Người</option>
                        <option value="Sân 11" {{ request('type') == 'Sân 11' ? 'selected' : '' }}>Sân 11 Người</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="col-lg-2 col-md-6">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="fas fa-search me-1"></i>Tìm
                    </button>
                </div>
            </div>

            <!-- Ngày hiển thị + Legend -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="avail-date-label">
                        <i class="fas fa-calendar-day me-1 text-primary"></i>
                        {{ \Carbon\Carbon::parse($date)->locale('vi')->isoFormat('dddd, DD/MM/YYYY') }}
                        @if($date === now()->toDateString())
                            <span class="badge bg-success ms-1">Hôm nay</span>
                        @endif
                    </span>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="legend-item"><span class="legend-dot dot-free"></span>Còn trống</span>
                    <span class="legend-item"><span class="legend-dot dot-booked"></span>Đã đặt</span>
                    <span class="legend-item"><span class="legend-dot dot-maintenance"></span>Bảo trì</span>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Arena List -->
<section class="py-4" style="background:#f4f6f8; min-height:500px">
    <div class="container">

        @if($arenas->isEmpty())
            <div class="text-center py-5" data-aos="fade-up">
                <i class="fas fa-search-minus fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Không tìm thấy sân nào</h4>
                <p class="text-muted">Thử lại với từ khóa khác.</p>
                <a href="{{ route('arenas.available', ['date' => $date]) }}" class="btn btn-primary mt-2">
                    <i class="fas fa-redo me-1"></i>Xóa bộ lọc
                </a>
            </div>
        @else
            <div class="mb-3 text-muted small">
                {{ $arenas->total() }} sân — click vào vùng xanh để đặt sân
            </div>

            @foreach($arenas as $arena)
                @php $bookedRanges = $bookingsByArena[$arena->id] ?? []; @endphp
                <div class="avail-card" data-date="{{ $date }}" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 5) * 50 }}">

                    <!-- Left: Info -->
                    <div class="avail-card-left">
                        <div class="avail-thumb">
                            @if($arena->image)
                                <img src="{{ asset('storage/' . $arena->image) }}" alt="{{ $arena->name }}">
                            @else
                                <div class="avail-thumb-ph"><i class="fas fa-futbol"></i></div>
                            @endif
                        </div>
                        <div class="avail-meta">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="avail-name">{{ $arena->name }}</span>
                                <span class="avail-type">{{ $arena->type }}</span>
                            </div>
                            <div class="avail-location text-muted">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                {{ Str::limit($arena->location, 40) }}
                            </div>
                            <div class="avail-price">
                                <span class="fw-bold text-success">{{ number_format($arena->price) }}đ</span>
                                <span class="text-muted small">/h</span>
                            </div>
                        </div>
                    </div>

                    <!-- Center: Timeline -->
                    <div class="avail-timeline-wrap">
                        <!-- Hour labels -->
                        <div class="tl-labels">
                            @for($h = 6; $h <= 24; $h += 2)
                                <span style="left: {{ (($h - 6) / 18) * 100 }}%">{{ $h < 24 ? sprintf('%02d', $h) : '00' }}</span>
                            @endfor
                        </div>

                        <!-- Bar -->
                        @if($arena->isMaintenance())
                            <div class="tl-bar tl-bar-maintenance">
                                <span><i class="fas fa-wrench me-1"></i>Đang bảo trì — không thể đặt</span>
                            </div>
                        @else
                            <div class="tl-bar"
                                 data-arena="{{ $arena->id }}"
                                 data-booked="{{ json_encode($bookedRanges) }}"
                                 title="Click vùng xanh để đặt sân">
                            </div>
                        @endif

                        <!-- Hour tick marks -->
                        <div class="tl-ticks">
                            @for($h = 6; $h <= 24; $h++)
                                <span style="left: {{ (($h - 6) / 18) * 100 }}%"></span>
                            @endfor
                        </div>
                    </div>

                    <!-- Right: Badge + Button -->
                    <div class="avail-card-right">
                        <span class="free-badge">...</span>
                        @if($arena->isMaintenance())
                            <button class="btn btn-sm btn-warning fw-semibold mt-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#maintenanceModal"
                                    data-arena-name="{{ $arena->name }}">
                                <i class="fas fa-wrench me-1"></i>Bảo Trì
                            </button>
                        @else
                            <a href="{{ route('bookings.create', $arena) }}?date={{ $date }}"
                               class="btn btn-sm btn-primary fw-semibold mt-2">
                                <i class="fas fa-calendar-check me-1"></i>Đặt Sân
                            </a>
                        @endif
                    </div>

                </div>
            @endforeach

            <!-- Pagination -->
            @if($arenas->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $arenas->links() }}
                </div>
            @endif
        @endif
    </div>
</section>

{{-- ===================== STYLES ===================== --}}
<style>
/* Hero */
.avail-hero {
    padding: 130px 0 70px;
    background: var(--clr-dark-900, #0d1117);
    position: relative;
    overflow: hidden;
}
.avail-hero-glow {
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 30% 0%, rgba(34,197,94,.14) 0%, transparent 60%),
        radial-gradient(ellipse at 70% 100%, rgba(16,185,129,.08) 0%, transparent 60%);
}
.avail-hero-badge {
    display: inline-flex;
    align-items: center;
    background: rgba(34,197,94,.15);
    color: #4ade80;
    border: 1px solid rgba(34,197,94,.3);
    border-radius: 50px;
    padding: 6px 20px;
    font-size: .85rem;
    font-weight: 600;
}

/* Filter bar */
.avail-filter-bar {
    position: sticky;
    top: 70px;
    z-index: 100;
    background: #fff;
    border-bottom: 1px solid #e9ecef;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    padding: 14px 0;
}
.avail-input-group .form-control,
.avail-input-group .input-group-text,
.avail-select { height: 44px; border-color: #dee2e6; }
.avail-input-group .form-control:focus { border-color: #4ade80; box-shadow: 0 0 0 3px rgba(74,222,128,.15); }
.avail-date-label { font-size: .9rem; font-weight: 600; color: #374151; }

/* Legend */
.legend-item { display: inline-flex; align-items: center; gap: 6px; font-size: .8rem; color: #6b7280; font-weight: 500; }
.legend-dot { width: 14px; height: 14px; border-radius: 4px; display: inline-block; }
.dot-free { background: #22c55e; }
.dot-booked { background: #ef4444; }
.dot-maintenance { background: #f59e0b; }

/* Arena Card */
.avail-card {
    display: flex;
    align-items: center;
    gap: 16px;
    background: #fff;
    border-radius: 16px;
    padding: 14px 18px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    transition: transform .2s, box-shadow .2s;
}
.avail-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }

/* Thumb */
.avail-card-left { display: flex; align-items: center; gap: 12px; flex-shrink: 0; min-width: 220px; }
.avail-thumb { width: 80px; height: 58px; border-radius: 10px; overflow: hidden; flex-shrink: 0; }
.avail-thumb img { width: 100%; height: 100%; object-fit: cover; }
.avail-thumb-ph { width: 100%; height: 100%; background: #e9ecef; display: flex; align-items: center; justify-content: center; color: #adb5bd; font-size: 1.3rem; }

/* Meta */
.avail-name { font-size: .95rem; font-weight: 700; color: #1a202c; }
.avail-type { background: rgba(34,197,94,.12); color: #166534; border: 1px solid rgba(34,197,94,.3); border-radius: 20px; padding: 1px 9px; font-size: .72rem; font-weight: 600; white-space: nowrap; }
.avail-location { font-size: .75rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
.avail-price { font-size: .85rem; margin-top: 2px; }

/* Timeline */
.avail-timeline-wrap { flex: 1; min-width: 0; padding: 6px 0 18px; }

.tl-labels {
    position: relative;
    height: 18px;
    margin-bottom: 4px;
}
.tl-labels span {
    position: absolute;
    transform: translateX(-50%);
    font-size: .68rem;
    color: #9ca3af;
    font-weight: 500;
    user-select: none;
}

.tl-bar {
    position: relative;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(90deg, #dcfce7 0%, #bbf7d0 100%);
    cursor: pointer;
    overflow: hidden;
    border: 1px solid #86efac;
    transition: box-shadow .2s;
}
.tl-bar:hover { box-shadow: 0 0 0 3px rgba(34,197,94,.25); }

.tl-bar-maintenance {
    background: repeating-linear-gradient(
        45deg,
        #fef3c7,
        #fef3c7 8px,
        #fde68a 8px,
        #fde68a 16px
    );
    cursor: not-allowed;
    border-color: #fbbf24;
    display: flex;
    align-items: center;
    justify-content: center;
}
.tl-bar-maintenance span { font-size: .75rem; font-weight: 600; color: #92400e; }

/* Booked blocks (injected by JS) */
.tl-booked {
    position: absolute;
    top: 0;
    height: 100%;
    background: linear-gradient(90deg, #ef4444, #dc2626);
    border-radius: 4px;
    pointer-events: none;
}
.tl-booked::after {
    content: '';
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(
        45deg,
        rgba(255,255,255,0) 0,
        rgba(255,255,255,0) 4px,
        rgba(255,255,255,.15) 4px,
        rgba(255,255,255,.15) 6px
    );
    border-radius: 4px;
}

/* Now marker (injected by JS for today) */
.tl-now-marker {
    position: absolute;
    top: -4px;
    width: 2px;
    height: calc(100% + 8px);
    background: #6366f1;
    border-radius: 2px;
    z-index: 5;
}
.tl-now-marker::before {
    content: 'Hiện tại';
    position: absolute;
    top: -18px;
    left: 50%;
    transform: translateX(-50%);
    font-size: .6rem;
    color: #6366f1;
    white-space: nowrap;
    font-weight: 700;
}

/* Tick marks */
.tl-ticks {
    position: relative;
    height: 6px;
}
.tl-ticks span {
    position: absolute;
    width: 1px;
    height: 6px;
    background: #d1d5db;
    transform: translateX(-50%);
}
.tl-ticks span:first-child,
.tl-ticks span:last-child { background: #9ca3af; }

/* Right side */
.avail-card-right { flex-shrink: 0; text-align: center; min-width: 90px; display: flex; flex-direction: column; align-items: center; }

/* Free badge */
.free-badge {
    display: inline-block;
    font-size: .72rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    white-space: nowrap;
}
.badge-free { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.badge-full { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.badge-maintenance { background: #fef3c7; color: #92400e; border: 1px solid #fbbf24; }

/* Pagination */
.pagination .page-item .page-link { border-radius: 10px; margin: 0 4px; border: none; color: #374151; box-shadow: 0 2px 5px rgba(0,0,0,.05); }
.pagination .page-item.active .page-link { background: var(--gradient-primary, linear-gradient(135deg,#4ade80,#16a34a)); color: #fff; }

/* Responsive */
@media (max-width: 992px) {
    .avail-card { flex-wrap: wrap; }
    .avail-card-left { min-width: 0; width: 100%; }
    .avail-timeline-wrap { width: 100%; overflow-x: auto; min-width: 300px; }
    .avail-card-right { width: 100%; flex-direction: row; justify-content: space-between; align-items: center; }
}
@media (max-width: 576px) {
    .avail-thumb { width: 60px; height: 44px; }
    .tl-bar { height: 30px; }
}
</style>

{{-- ===================== SCRIPT ===================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const BASE     = 6 * 60;   // 06:00 = 360 min
    const TOTAL    = 18 * 60;  // 1080 min (06:00–24:00)
    const today    = '{{ now()->toDateString() }}';
    const pageDate = '{{ $date }}';

    function toMin(t) {
        const [h, m] = t.split(':').map(Number);
        return h * 60 + m;
    }
    function toTime(min) {
        const h = Math.floor(min / 60) % 24;
        const m = min % 60;
        return String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0');
    }
    function mergeRanges(ranges) {
        if (!ranges.length) return [];
        const s = ranges.slice().sort((a, b) => a[0] - b[0]);
        const r = [s[0].slice()];
        for (let i = 1; i < s.length; i++) {
            const last = r[r.length - 1];
            if (s[i][0] <= last[1]) last[1] = Math.max(last[1], s[i][1]);
            else r.push(s[i].slice());
        }
        return r;
    }

    document.querySelectorAll('.tl-bar[data-arena]').forEach(function (bar) {
        const raw          = bar.dataset.booked || '[]';
        const arenaId      = bar.dataset.arena;
        const card         = bar.closest('.avail-card');
        const date         = card.dataset.date;
        const badge        = card.querySelector('.free-badge');

        let bookedRanges;
        try { bookedRanges = JSON.parse(raw); } catch(e) { bookedRanges = []; }

        const merged = mergeRanges(
            bookedRanges
                .map(r => [toMin(r.start), toMin(r.end)])
                .filter(([s, e]) => e > s)
        );

        // --- Compute free minutes ---
        let bookedMin = 0;
        merged.forEach(([s, e]) => {
            const clampS = Math.max(s, BASE);
            const clampE = Math.min(e, BASE + TOTAL);
            if (clampE > clampS) bookedMin += clampE - clampS;
        });
        const freeMin  = TOTAL - bookedMin;
        const freeH    = Math.floor(freeMin / 60);
        const freeM    = freeMin % 60;

        if (badge) {
            if (freeMin === 0) {
                badge.textContent = 'Hết chỗ';
                badge.className = 'free-badge badge-full';
            } else {
                let txt = '';
                if (freeH > 0 && freeM > 0) txt = freeH + 'h' + freeM + 'p trống';
                else if (freeH > 0) txt = freeH + 'h trống';
                else txt = freeM + 'p trống';
                badge.textContent = txt;
                badge.className = 'free-badge badge-free';
            }
        }

        // --- Render booked blocks ---
        merged.forEach(function ([startM, endM]) {
            const s    = startM - BASE;
            const e    = endM   - BASE;
            if (e <= 0 || s >= TOTAL) return;
            const left  = (Math.max(0, s) / TOTAL) * 100;
            const width = ((Math.min(TOTAL, e) - Math.max(0, s)) / TOTAL) * 100;
            if (width <= 0) return;

            const block = document.createElement('div');
            block.className = 'tl-booked';
            block.style.left  = left + '%';
            block.style.width = width + '%';
            block.title       = 'Đã đặt: ' + toTime(startM) + ' – ' + toTime(endM);
            bar.appendChild(block);
        });

        // --- "Now" marker for today ---
        if (pageDate === today) {
            const now = new Date();
            const nowMin = now.getHours() * 60 + now.getMinutes();
            if (nowMin >= BASE && nowMin <= BASE + TOTAL) {
                const pct = ((nowMin - BASE) / TOTAL) * 100;
                const marker = document.createElement('div');
                marker.className = 'tl-now-marker';
                marker.style.left = pct + '%';
                bar.appendChild(marker);
            }
        }

        // --- Click to navigate to booking ---
        bar.addEventListener('click', function (e) {
            // Ignore clicks on booked blocks
            if (e.target.classList.contains('tl-booked') || e.target.classList.contains('tl-now-marker')) return;

            const rect      = bar.getBoundingClientRect();
            const pct       = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
            const clickMin  = Math.floor(pct * TOTAL / 30) * 30 + BASE;

            const isBooked = merged.some(([s, en]) => clickMin >= s && clickMin < en);
            if (!isBooked) {
                window.location.href = '/dat-san/' + arenaId + '?date=' + date;
            }
        });

        // --- Cursor feedback on hover ---
        bar.addEventListener('mousemove', function (e) {
            const rect     = bar.getBoundingClientRect();
            const pct      = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
            const hoverMin = Math.floor(pct * TOTAL / 30) * 30 + BASE;
            const isBooked = merged.some(([s, en]) => hoverMin >= s && hoverMin < en);
            bar.style.cursor = isBooked ? 'not-allowed' : 'pointer';

            if (!isBooked) {
                bar.title = 'Còn trống lúc ' + toTime(hoverMin) + ' — click để đặt sân';
            } else {
                bar.title = 'Khung giờ này đã có người đặt';
            }
        });
    });

    // Maintenance badge
    document.querySelectorAll('.avail-card').forEach(card => {
        const badge = card.querySelector('.free-badge');
        if (badge && badge.textContent === '...') {
            badge.textContent = 'Bảo trì';
            badge.className = 'free-badge badge-maintenance';
        }
    });
});
</script>

@endsection
