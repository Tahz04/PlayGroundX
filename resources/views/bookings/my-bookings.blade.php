@extends('layouts.app')

@section('title', 'Lịch Đặt Sân - PlayGroundX')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5 align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill py-2 px-3 mb-3">Lịch đặt sân</span>
                <h1 class="display-6 mb-3">Theo dõi các lịch đặt sân của bạn</h1>
                <p class="text-muted fs-6">Xem trạng thái, thời gian và thông tin sân đã đặt.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            </div>
        @endif

        <div class="row g-4">
        @forelse($bookings as $booking)
            <div class="col-lg-6">
                <div class="card border-0 rounded-4 shadow-sm bg-white overflow-hidden">
                    <div class="row g-0">
                        <div class="col-4">
                            @if($booking->arena->image)
    <img src="{{ asset('storage/' . $booking->arena->image) }}" class="img-fluid h-100 object-fit-cover" alt="{{ $booking->arena->name }}">
@else
    <div class="bg-secondary d-flex align-items-center justify-content-center h-100" style="min-height: 150px;">
        <i class="fas fa-image fa-2x text-white opacity-50"></i>
    </div>
@endif
                        </div>
                        <div class="col-8">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="card-title fw-bold mb-1 text-truncate">{{ $booking->arena->name }}</h5>
                                        <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($booking->arena->location, 45) }}</p>
                                    </div>
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
                                    <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 small">{{ $statusText }}</span>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Ngày đặt</div>
                                        <div class="fw-bold">{{ date('d/m/Y', strtotime($booking->date)) }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="text-muted small">Giờ chơi</div>
                                        <div class="fw-bold text-primary">{{ $booking->timeSlot->formattedTime() }}</div>
                                    </div>
                                </div>

                                @if($booking->status == 'pending')
                                    <div class="mt-4 text-end">
                                        <form action="{{ route('bookings.user-cancel', $booking) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy yêu cầu này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">Hủy yêu cầu</button>
                                        </form>
                                    </div>
                                @elseif($booking->status == 'confirmed' || $booking->status == 'paid')
                                    <div class="mt-4 text-end">
                                        <a href="https://zalo.me/0986049032" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            <i class="fas fa-comments me-1"></i> Thương lượng
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 rounded-4 shadow-sm bg-white py-5">
                    <div class="text-center px-4">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-4 opacity-25"></i>
                        <h4 class="mb-3">Bạn chưa có lịch đặt sân nào</h4>
                        <p class="text-muted mb-4">Hãy khám phá các sân bóng và đặt một trận đấu ngay để lần sau có lịch sử đặt sân đẹp hơn.</p>
                        <a href="{{ route('arenas.index') }}" class="btn btn-hero-primary btn-lg px-5 py-3">Tìm Sân Ngay</a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
