@extends('layouts.app')

@section('title', 'Chi Tiết Sân - ' . $arena->name)

@section('content')
<section class="py-5 bg-light" style="margin-top: 80px;">
    <div class="container">
        <!-- Breadcrumb & Back button -->
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('arenas.index') }}" class="btn btn-outline-secondary rounded-circle shadow-sm me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="fw-bold mb-0">Chi tiết sân bóng</h2>
        </div>

        <div class="row g-4">
            <!-- Cột trái: Hình ảnh -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <!-- Carousel Hình ảnh -->
                    <div id="arenaImageCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <!-- Ảnh đại diện -->
                            <div class="carousel-item active">
                                @if($arena->image)
                                    <img src="{{ Storage::url($arena->image) }}" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="{{ $arena->name }}">
                                @else
                                    <img src="https://via.placeholder.com/800x400?text=Chua+co+anh" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="No image">
                                @endif
                            </div>
                            <!-- Ảnh phụ 1 -->
                            @if($arena->image_1)
                            <div class="carousel-item">
                                <img src="{{ Storage::url($arena->image_1) }}" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="Ảnh phụ 1">
                            </div>
                            @endif
                            <!-- Ảnh phụ 2 -->
                            @if($arena->image_2)
                            <div class="carousel-item">
                                <img src="{{ Storage::url($arena->image_2) }}" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="Ảnh phụ 2">
                            </div>
                            @endif
                        </div>
                        @if($arena->image_1 || $arena->image_2)
                        <button class="carousel-control-prev" type="button" data-bs-target="#arenaImageCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon shadow-lg rounded-circle bg-dark p-3" aria-hidden="true"></span>
                            <span class="visually-hidden">Trước</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#arenaImageCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon shadow-lg rounded-circle bg-dark p-3" aria-hidden="true"></span>
                            <span class="visually-hidden">Sau</span>
                        </button>
                        @endif
                    </div>
                </div>

                <!-- Bản đồ nhỏ -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-map-marked-alt text-primary me-2"></i>Bản đồ vị trí</h5>
                        <div id="miniMap" style="height: 300px; width: 100%; border-radius: 12px; z-index: 1;"></div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Thông tin -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                    <div class="card-body p-4 p-xl-5">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h1 class="h3 fw-bold mb-0 text-dark">{{ $arena->name }}</h1>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2 ms-2">
                                <i class="fas fa-check-circle me-1"></i> Sẵn sàng
                            </span>
                        </div>

                        <div class="d-flex gap-2 mb-4">
                            <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                                <i class="fas fa-futbol me-1"></i> {{ $arena->type }}
                            </span>
                        </div>

                        <h2 class="text-primary fw-bold mb-4">
                            {{ number_format($arena->price, 0, ',', '.') }} VNĐ <span class="text-muted fs-6 fw-normal">/ giờ</span>
                        </h2>

                        <div class="mb-4">
                            <h5 class="fw-bold mb-2">Vị trí</h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i> {{ $arena->location }}
                            </p>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-bold mb-2">Mô tả sân</h5>
                            <p class="text-muted" style="line-height: 1.6;">
                                Đây là sân bóng đạt chuẩn, mặt cỏ nhân tạo chất lượng cao. Hệ thống chiếu sáng hiện đại đảm bảo tốt cho các trận đấu vào buổi tối. Khu vực an ninh, có chỗ để xe rộng rãi và dịch vụ nước uống tại sân.
                            </p>
                        </div>

                        <hr class="my-4 opacity-10">

                        <a href="{{ route('bookings.create', $arena->id) }}" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-pill shadow-sm" style="font-size: 1.1rem;">
                            <i class="far fa-calendar-alt me-2"></i> Đặt Sân Ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var lat = {{ $arena->latitude }};
        var lng = {{ $arena->longitude }};
        var name = "{{ $arena->name }}";

        // Khởi tạo bản đồ mini
        var map = L.map('miniMap').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Thêm marker
        L.marker([lat, lng]).addTo(map)
            .bindPopup("<b>" + name + "</b><br>Vị trí hiện tại.")
            .openPopup();
    });
</script>
@endsection
