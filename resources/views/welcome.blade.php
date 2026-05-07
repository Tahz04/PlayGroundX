@extends('layouts.app')

@section('title', 'PlayGroundX - Hệ Thống Đặt Sân Bóng Đá Trực Tuyến Hàng Đầu Việt Nam')

@section('content')

    <!-- Page Loader -->
    <div class="page-loader">
        <div class="loader-content">
            <div class="loader-icon"><i class="fas fa-futbol"></i></div>
            <div class="loader-text">PlayGroundX</div>
        </div>
    </div>

    <!-- ==================== HERO SECTION ==================== -->
    <section class="hero-section" id="hero">
        <div class="hero-bg">
            <img src="{{ asset('images/hero-banner.png') }}" alt="Sân bóng đá PlayGroundX" loading="eager">
        </div>
        <div class="hero-overlay"></div>

        <!-- Floating Particles -->
        <div class="hero-particles">
            @for ($i = 1; $i <= 15; $i++)
                <div class="particle"></div>
            @endfor
        </div>

        <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
            <div class="hero-badge">
                <i class="fas fa-bolt"></i>
                <span>#1 Đặt Sân Tại Việt Nam</span>
            </div>

            <div class="promotion-badge mt-3 mb-3" data-aos="fade-left" data-aos-delay="500">
                <i class="fas fa-gift text-warning me-2"></i>
                <span class="text-white fw-bold">ƯU ĐÃI: Giảm 10% khi đặt trên 3 giờ!</span>
            </div>

            <h1 class="hero-title">
                Đặt Sân Bóng Đá<br>
                <span class="highlight" id="heroTyping">Nhanh Chóng</span>
                <span class="typing-cursor" style="color: var(--clr-primary-400); animation: blink 1s step-end infinite;">|</span>
            </h1>

            <p class="hero-subtitle">
                Hàng trăm sân bóng chất lượng, đặt sân nhanh, thanh toán an toàn và uy tín.
            </p>

            <div class="hero-actions">
                <a href="{{ route('map') }}" class="btn-hero-primary">
                    <i class="fas fa-search"></i>
                    Đặt Sân Ngay
                </a>
                <a href="#tinh-nang" class="btn-hero-secondary">
                    <i class="fas fa-play-circle"></i>
                    Khám Phá
                </a>
            </div>

            <div class="hero-stats" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-item">
                    <span class="stat-number" data-count="500" data-suffix="+">0</span>
                    <span class="stat-label">Sân Bóng</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="25000" data-suffix="+">0</span>
                    <span class="stat-label">Lượt Đặt</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="15000" data-suffix="+">0</span>
                    <span class="stat-label">Khách Hàng</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="63" data-suffix="">0</span>
                    <span class="stat-label">Tỉnh Thành</span>
                </div>
            </div>
        </div>

        <div class="scroll-indicator">
            <span>Cuộn xuống</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <style>
        @keyframes blink { 50% { opacity: 0; } }
    </style>

    <!-- ==================== QUICK BOOKING ==================== -->
    <section class="booking-section">
        <div class="container">
            <div class="booking-card" data-aos="fade-up">
                <div class="row align-items-center mb-3">
                    <div class="col-md-8">
                        <h2 class="booking-title"><i class="fas fa-bolt text-warning me-2"></i>Đặt Sân Nhanh</h2>
                        <p class="booking-subtitle mb-0">Tìm và đặt sân bóng phù hợp chỉ trong vài giây</p>
                    </div>
                </div>

                <form id="quickBookingForm" method="GET" action="{{ route('arenas.available') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-icon-wrap">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <select class="form-select" id="bookingLocation" name="search">
                                    <option value="" selected>Chọn khu vực</option>
                                    <option value="Hà Nội">Hà Nội</option>
                                    <option value="Đà Nẵng">Đà Nẵng</option>
                                    <option value="Huế">Huế</option>
                                    <option value="Đà Lạt">Đà Lạt</option>
                                    <option value="TP.HCM">TP. Hồ Chí Minh</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-icon-wrap">
                                <i class="fas fa-calendar-alt input-icon"></i>
                                <input type="date" class="form-control" id="bookingDate" name="date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-icon-wrap">
                                <i class="fas fa-clock input-icon"></i>
                                <select class="form-select" id="bookingTime">
                                    <option selected disabled>Giờ chơi</option>
                                    <option>06:00 - 07:30</option>
                                    <option>07:30 - 09:00</option>
                                    <option>09:00 - 10:30</option>
                                    <option>15:00 - 16:30</option>
                                    <option>16:30 - 18:00</option>
                                    <option>18:00 - 19:30</option>
                                    <option>19:30 - 21:00</option>
                                    <option>21:00 - 22:30</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-icon-wrap">
                                <i class="fas fa-futbol input-icon"></i>
                                <select class="form-select" id="bookingType" name="type">
                                    <option value="" selected>Loại sân</option>
                                    <option value="Sân 5">Sân 5</option>
                                    <option value="Sân 7">Sân 7</option>
                                    <option value="Sân 11">Sân 11</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn-search-booking">
                                <i class="fas fa-search"></i>
                                Tìm Sân
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- ==================== FEATURES ==================== -->
    <section class="features-section" id="tinh-nang">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <div class="section-badge">
                    <i class="fas fa-star"></i>
                    <span>Tại sao chọn chúng tôi</span>
                </div>
                <h2 class="section-title">Trải Nghiệm Đặt Sân <span class="accent">Vượt Trội</span></h2>
                <p class="section-desc">PlayGroundX mang đến giải pháp đặt sân thông minh, hiện đại và tiện lợi nhất cho người chơi bóng đá</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="0">
                    <div class="feature-card">
                        <div class="feature-icon icon-green">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="feature-title">Đặt Sân Siêu Nhanh</h3>
                        <p class="feature-desc">Chỉ cần 3 bước đơn giản: chọn sân, chọn giờ, xác nhận. Đặt sân trong vòng 30 giây!</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon icon-amber">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">Thanh Toán An Toàn</h3>
                        <p class="feature-desc">Hỗ trợ đa dạng phương thức thanh toán: MoMo, ZaloPay, thẻ ngân hàng, tiền mặt.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon icon-blue">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h3 class="feature-title">Phủ Sóng Toàn Quốc</h3>
                        <p class="feature-desc">Hệ thống hơn 500+ sân bóng chất lượng cao tại 63 tỉnh thành trên cả nước.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon icon-purple">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="feature-title">Hỗ Trợ 24/7</h3>
                        <p class="feature-desc">Đội ngũ hỗ trợ tận tâm, sẵn sàng giải đáp mọi thắc mắc bất cứ lúc nào.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- ==================== PITCHES ==================== -->
<section class="pitches-section" id="san-bong">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-futbol"></i>
                <span>Sân bóng nổi bật</span>
            </div>
            <h2 class="section-title">Sân Bóng <span class="accent">Hàng Đầu</span></h2>
            <p class="section-desc">Những sân bóng chất lượng cao, được đánh giá tốt nhất bởi cộng đồng người chơi</p>
        </div>

        <div class="row g-4">
            @forelse($arenas as $arena)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="pitch-card">
                        <div class="pitch-image">
                            @if($arena->image)
                                <img src="{{ asset('storage/' . $arena->image) }}" alt="{{ $arena->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                            @else
                                <div style="width: 100%; height: 200px; background: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            @if($arena->isMaintenance())
                                <span class="pitch-badge maintenance"><i class="fas fa-wrench me-1"></i>Đang bảo trì</span>
                            @elseif($arena->isActive())
                                <span class="pitch-badge available"><i class="fas fa-check-circle me-1"></i>Sẵn sàng</span>
                            @else
                                <span class="pitch-badge unavailable"><i class="fas fa-times-circle me-1"></i>Ngừng hoạt động</span>
                            @endif
                            
                            <span class="pitch-type-badge"><i class="fas fa-users me-1"></i>{{ $arena->type }}</span>
                            <div class="pitch-overlay"></div>
                        </div>
                        <div class="pitch-info">
                            <h3 class="pitch-name">{{ $arena->name }}</h3>
                            <div class="pitch-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ Str::limit($arena->location, 50) }}</span>
                            </div>
                            <div class="pitch-meta">
                                <div class="pitch-price">{{ number_format($arena->price) }}đ <span>/ giờ</span></div>
                                <div class="pitch-rating"><i class="fas fa-star"></i> 4.9</div>
                            </div>
                            @if($arena->isMaintenance())
                                <button class="btn-book btn-book-maintenance w-100"
                                        data-bs-toggle="modal"
                                        data-bs-target="#maintenanceModal"
                                        data-arena-name="{{ $arena->name }}">
                                    <i class="fas fa-wrench"></i>
                                    Đang Bảo Trì
                                </button>
                            @else
                                <a href="{{ route('bookings.create', $arena) }}" class="btn-book">
                                    <i class="fas fa-calendar-check"></i>
                                    Đặt Sân Ngay
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Đang cập nhật danh sách sân...</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('arenas.index') }}" class="btn-hero-secondary" style="color: var(--clr-dark-900); border-color: var(--clr-dark-300);">
                <i class="fas fa-th-large"></i>
                Xem Tất Cả Sân Bóng
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>



    <!-- ==================== TESTIMONIALS ==================== -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <div class="section-badge">
                    <i class="fas fa-comments"></i>
                    <span>Đánh giá từ khách hàng</span>
                </div>
                <h2 class="section-title">Khách Hàng <span class="accent">Nói Gì</span></h2>
                <p class="section-desc">Hàng ngàn khách hàng hài lòng với dịch vụ đặt sân của PlayGroundX</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="0">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">
                            Dễ sử dụng, đặt sân nhanh gọn! Trước đây phải gọi điện hỏi từng sân, giờ chỉ cần vài click là xong. Sân bóng chất lượng, giá cả hợp lý.
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar-placeholder">NT</div>
                            <div>
                                <div class="testimonial-name">Nguyễn Thanh Tùng</div>
                                <div class="testimonial-role">Đội trưởng FC Thành Công</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">
                            PlayGroundX giúp team mình tiết kiệm rất nhiều thời gian. Lịch đặt rõ ràng, có thể đặt trước cả tuần. Highly recommend cho anh em bóng đá!
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar-placeholder">LM</div>
                            <div>
                                <div class="testimonial-name">Lê Minh Đức</div>
                                <div class="testimonial-role">Thành viên CLB Weekend Warriors</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="testimonial-text">
                            Là chủ sân bóng, tôi rất hài lòng khi hợp tác với PlayGroundX. Lượng khách đặt sân tăng 40% kể từ khi tham gia nền tảng này!
                        </p>
                        <div class="testimonial-author">
                            <div class="testimonial-avatar-placeholder">TH</div>
                            <div>
                                <div class="testimonial-name">Trần Hữu Phong</div>
                                <div class="testimonial-role">Chủ sân bóng Phú Mỹ Sport</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================== CTA ==================== -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-card" data-aos="zoom-in">
                <h2 class="cta-title">
                    <i class="fas fa-futbol me-2"></i>
                    Sẵn Sàng Cho Trận Đấu Tiếp Theo?
                </h2>
                <p class="cta-desc">
                    Đăng ký miễn phí ngay hôm nay và nhận ưu đãi giảm 20% cho lần đặt sân đầu tiên! 
                    Đặc biệt, <span class="fw-bold text-warning">giảm thêm 10%</span> cho mọi lượt đặt sân từ 3 tiếng trở lên.
                </p>
                <a href="#" class="btn-cta">
                    <i class="fas fa-rocket"></i>
                    Bắt Đầu Ngay - Miễn Phí
                </a>
            </div>
        </div>
    </section>

    <!-- ==================== CONTACT ==================== -->
    <section class="contact-section" id="lien-he">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <div class="section-badge">
                    <i class="fas fa-envelope"></i>
                    <span>Liên hệ với chúng tôi</span>
                </div>
                <h2 class="section-title">Liên Hệ <span class="accent">Hỗ Trợ</span></h2>
                <p class="section-desc">Bạn cần hỗ trợ? Đội ngũ chúng tôi luôn sẵn sàng giúp đỡ bạn</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="contact-info-card">
                        <h3 class="contact-info-title">Thông Tin Liên Hệ</h3>
                        <p class="contact-info-desc">Liên hệ trực tiếp hoặc gửi tin nhắn cho chúng tôi</p>

                        <div class="contact-item">
                            <div class="contact-item-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <div class="contact-item-label">Địa chỉ</div>
                                <div class="contact-item-value">123 Nguyễn Văn Linh, Q.7, TP.HCM</div>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-item-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <div class="contact-item-label">Hotline</div>
                                <div class="contact-item-value">0909 123 456</div>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-item-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <div class="contact-item-label">Email</div>
                                <div class="contact-item-value">info@playgroundx.vn</div>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-item-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="contact-item-label">Giờ làm việc</div>
                                <div class="contact-item-value">06:00 - 23:00 (Hàng ngày)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7" data-aos="fade-left">
                    <div class="contact-form-card">
                        <h3 class="contact-form-title">Gửi Tin Nhắn</h3>
                        <p class="contact-form-desc">Điền thông tin bên dưới, chúng tôi sẽ phản hồi trong thời gian sớm nhất</p>

                        <form id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="contactName" class="form-label fw-semibold">Họ và Tên</label>
                                    <input type="text" class="form-control" id="contactName" placeholder="Nhập họ tên">
                                </div>
                                <div class="col-md-6">
                                    <label for="contactPhone" class="form-label fw-semibold">Số Điện Thoại</label>
                                    <input type="tel" class="form-control" id="contactPhone" placeholder="Nhập số điện thoại">
                                </div>
                                <div class="col-12">
                                    <label for="contactEmail" class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control" id="contactEmail" placeholder="Nhập email">
                                </div>
                                <div class="col-12">
                                    <label for="contactSubject" class="form-label fw-semibold">Chủ Đề</label>
                                    <select class="form-select" id="contactSubject">
                                        <option selected disabled>Chọn chủ đề</option>
                                        <option>Hỗ trợ đặt sân</option>
                                        <option>Hợp tác kinh doanh</option>
                                        <option>Góp ý & phản hồi</option>
                                        <option>Khác</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="contactMessage" class="form-label fw-semibold">Nội Dung</label>
                                    <textarea class="form-control" id="contactMessage" rows="4" placeholder="Nhập nội dung tin nhắn..."></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-send">
                                        <i class="fas fa-paper-plane"></i>
                                        Gửi Tin Nhắn
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Back to Top -->
    <button id="backToTop" class="btn-back-to-top" aria-label="Lên đầu trang">
        <i class="fas fa-arrow-up"></i>
    </button>

    <style>
        .btn-book-maintenance {
            background: linear-gradient(135deg, #92400e, #d97706) !important;
            color: #fef3c7 !important;
            cursor: pointer !important;
            opacity: 1 !important;
            border: none;
        }
        .btn-book-maintenance:hover {
            background: linear-gradient(135deg, #b45309, #f59e0b) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217,119,6,.4);
        }
        .promotion-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 0.75rem 1.25rem;
            border-radius: 50px;
            border: 1px solid rgba(255, 193, 7, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0% { border-color: rgba(255, 193, 7, 0.3); }
            50% { border-color: rgba(255, 193, 7, 1); box-shadow: 0 0 15px rgba(255, 193, 7, 0.4); }
            100% { border-color: rgba(255, 193, 7, 0.3); }
        }

        .btn-back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--gradient-primary);
            color: #fff;
            border: none;
            font-size: 1.1rem;
            cursor: pointer;
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: var(--transition-base);
            box-shadow: var(--shadow-lg);
        }
        .btn-back-to-top.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .btn-back-to-top:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-glow);
        }
    </style>

@endsection
