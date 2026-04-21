@extends('layouts.app')

@section('title', 'Chi Tiết Hóa Đơn Đặt Sân')

@section('content')
<section class="py-5 bg-light" style="margin-top: 90px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                            <div>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 mb-2">Hóa đơn đặt sân</span>
                                <h2 class="h3 fw-bold mb-1">{{ $firstBooking->arena->name }}</h2>
                                <p class="text-muted mb-0">Ngày đặt: {{ date('d/m/Y', strtotime($firstBooking->date)) }}</p>
                            </div>
                            <div class="text-end">
                                @php
                                    $paymentStatusText = match ($paymentStatus) {
                                        'unpaid' => 'Chưa thanh toán',
                                        'pending' => 'Thanh toán tại sân',
                                        'paid' => 'Đã thanh toán',
                                        'failed' => 'Thanh toán lỗi',
                                        default => ucfirst((string) $paymentStatus),
                                    };
                                @endphp
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">{{ $paymentStatusText }}</span>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <div class="text-muted small">Khung giờ</div>
                                    <div class="fw-semibold">{{ $startClock }} - {{ $endClock }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <div class="text-muted small">Thời lượng</div>
                                    <div class="fw-semibold">{{ ($bookings->count() * 30) / 60 }} giờ</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <div class="text-muted small">Phương thức</div>
                                    <div class="fw-semibold">{{ $paymentMethod === 'bank_transfer' ? 'Chuyển khoản' : 'Tiền mặt' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Khung giờ</th>
                                        <th class="text-end">Đơn giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->timeSlot->formattedTime() }}</td>
                                            <td class="text-end">{{ number_format($booking->payment?->amount ?? $booking->arena->price) }}đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Tổng thanh toán</th>
                                        <th class="text-end text-primary fs-5">{{ number_format($totalAmount) }}đ</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('bookings.my-bookings') }}" class="btn btn-outline-secondary px-4">Xem lịch sử đặt sân</a>
                            @if($paymentMethod === 'bank_transfer')
                                <a href="{{ route('bookings.payment-transfer', ['bookings' => $bookingIdsParam]) }}" class="btn btn-primary px-4">Quay lại trang thanh toán</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
