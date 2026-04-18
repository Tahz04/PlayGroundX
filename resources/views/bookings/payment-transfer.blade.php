@extends('layouts.app')

@section('title', 'Thanh Toán Chuyển Khoản')

@section('content')
<section class="py-5 bg-light" style="margin-top: 90px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge bg-info text-dark rounded-pill px-3 py-2 mb-3">Thanh toán chuyển khoản</span>
                        <h2 class="h3 fw-bold mb-2">Hoàn tất thanh toán cho đơn đặt sân</h2>
                        <p class="text-muted mb-4">Vui lòng chuyển khoản đúng nội dung để hệ thống đối soát nhanh hơn.</p>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <div class="text-muted small">Sân</div>
                                    <div class="fw-semibold">{{ $firstBooking->arena->name }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light rounded-3 p-3 h-100">
                                    <div class="text-muted small">Tổng thanh toán</div>
                                    <div class="fw-semibold text-primary">{{ number_format($totalAmount) }}đ</div>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded-3 p-4 mb-4">
                            <div class="mb-2"><span class="text-muted">Ngân hàng:</span> <strong>Vietcombank</strong></div>
                            <div class="mb-2"><span class="text-muted">Số tài khoản:</span> <strong>0123456789</strong></div>
                            <div class="mb-2"><span class="text-muted">Chủ tài khoản:</span> <strong>PLAYGROUNDX CO., LTD</strong></div>
                            <div><span class="text-muted">Nội dung CK:</span> <strong>PGX {{ implode('-', $bookings->pluck('id')->all()) }}</strong></div>
                        </div>

                        <div class="alert alert-warning" style="border-radius: 12px; border: none;">
                            Sau khi chuyển khoản, admin sẽ xác nhận và cập nhật trạng thái đơn của bạn.
                        </div>

                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a href="{{ route('bookings.bill', ['bookings' => $bookingIdsParam]) }}" class="btn btn-primary px-4">Xem chi tiết bill</a>
                            <a href="{{ route('bookings.my-bookings') }}" class="btn btn-outline-secondary px-4">Xem lịch sử đặt sân</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
