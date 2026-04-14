@extends('layouts.app')

@section('title', 'Lịch Sử Đặt Sân - PlayGroundX')

@section('content')
<div class="container" style="margin-top: 120px; margin-bottom: 60px;">
    <div class="mb-5">
        <h2 class="fw-bold"><i class="fas fa-calendar-alt text-primary me-2"></i>Lịch Sử Đặt Sân</h2>
        <p class="text-muted">Theo dõi trạng thái các yêu cầu đặt sân của bạn</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(16,185,129,0.1);">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($bookings as $booking)
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                    <div class="row g-0">
                        <div class="col-4">
                            <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=300&fit=crop" class="img-fluid h-100 object-fit-cover" alt="Pitch">
                        </div>
                        <div class="col-8">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold mb-0 text-truncate">{{ $booking->arena->name }}</h5>
                                    @php
                                        $statusClass = [
                                            'pending' => 'bg-warning text-dark',
                                            'confirmed' => 'bg-success text-white',
                                            'cancelled' => 'bg-danger text-white',
                                            'completed' => 'bg-info text-white'
                                        ][$booking->status] ?? 'bg-secondary text-white';

                                        $statusText = [
                                            'pending' => 'Chờ xác nhận',
                                            'confirmed' => 'Đã xác nhận',
                                            'cancelled' => 'Đã hủy',
                                            'completed' => 'Hoàn thành'
                                        ][$booking->status] ?? $booking->status;
                                    @endphp
                                    <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 small" style="font-size: 0.7rem;">{{ $statusText }}</span>
                                </div>
                                
                                <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($booking->arena->location, 40) }}</p>
                                
                                <div class="d-flex gap-4">
                                    <div>
                                        <div class="text-muted small">Ngày đặt</div>
                                        <div class="fw-bold">{{ date('d/m/Y', strtotime($booking->date)) }}</div>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Giờ chơi</div>
                                        <div class="fw-bold text-primary">{{ $booking->timeSlot->formattedTime() }}</div>
                                    </div>
                                </div>
                                
                                @if($booking->status == 'pending')
                                    <div class="mt-3 text-end">
                                        <button class="btn btn-link text-danger text-decoration-none btn-sm fw-bold">Hủy yêu cầu</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="py-5 bg-white rounded-5 shadow-sm">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-4 opacity-25"></i>
                    <h4 class="text-muted">Bạn chưa có lịch đặt sân nào</h4>
                    <p class="text-muted">Hãy khám phá các sân bóng và đặt một trận đấu ngay!</p>
                    <a href="{{ route('arenas.index') }}" class="btn btn-primary px-4 py-2 mt-3" style="border-radius: 50px;">Tìm Sân Ngay</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
