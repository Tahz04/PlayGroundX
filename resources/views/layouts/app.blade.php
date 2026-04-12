<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PlayGroundX - Hệ thống đặt sân bóng đá trực tuyến hàng đầu Việt Nam. Đặt sân nhanh chóng, tiện lợi với giá tốt nhất.">
    <meta name="keywords" content="đặt sân bóng, sân bóng đá, thuê sân bóng, đặt sân online">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'PlayGroundX - Đặt Sân Bóng Đá Trực Tuyến')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-futbol brand-icon"></i>
                <span class="brand-text">Play<span class="brand-highlight">Ground</span>X</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            <i class="fas fa-home me-1"></i> Trang Chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#san-bong">
                            <i class="fas fa-map-marker-alt me-1"></i> Sân Bóng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#bang-gia">
                            <i class="fas fa-tags me-1"></i> Bảng Giá
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#lien-he">
                            <i class="fas fa-envelope me-1"></i> Liên Hệ
                        </a>
                    </li>
                </ul>

                <div class="nav-actions">
                    <a href="#" class="btn btn-outline-light btn-nav me-2" id="btnLogin">
                        <i class="fas fa-sign-in-alt me-1"></i> Đăng Nhập
                    </a>
                    <a href="#" class="btn btn-accent btn-nav" id="btnRegister">
                        <i class="fas fa-user-plus me-1"></i> Đăng Ký
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-wave">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" preserveAspectRatio="none">
                <path fill="currentColor" d="M0,64L48,58.7C96,53,192,43,288,48C384,53,480,75,576,80C672,85,768,75,864,64C960,53,1056,43,1152,42.7C1248,43,1344,53,1392,58.7L1440,64L1440,120L1392,120C1344,120,1248,120,1152,120C1056,120,960,120,864,120C768,120,672,120,576,120C480,120,384,120,288,120C192,120,96,120,48,120L0,120Z"></path>
            </svg>
        </div>

        <div class="footer-content">
            <div class="container">
                <div class="row g-4">
                    <!-- Brand -->
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-brand">
                            <a href="{{ url('/') }}" class="d-flex align-items-center mb-3">
                                <i class="fas fa-futbol brand-icon me-2"></i>
                                <span class="brand-text">Play<span class="brand-highlight">Ground</span>X</span>
                            </a>
                            <p class="footer-desc">
                                Hệ thống đặt sân bóng đá trực tuyến hàng đầu Việt Nam. 
                                Đặt sân nhanh chóng, tiện lợi với giá tốt nhất.
                            </p>
                            <div class="social-links">
                                <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                                <a href="#" class="social-link" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6">
                        <h5 class="footer-title">Liên Kết</h5>
                        <ul class="footer-links">
                            <li><a href="{{ url('/') }}"><i class="fas fa-chevron-right me-1"></i> Trang Chủ</a></li>
                            <li><a href="#san-bong"><i class="fas fa-chevron-right me-1"></i> Sân Bóng</a></li>
                            <li><a href="#bang-gia"><i class="fas fa-chevron-right me-1"></i> Bảng Giá</a></li>
                            <li><a href="#lien-he"><i class="fas fa-chevron-right me-1"></i> Liên Hệ</a></li>
                        </ul>
                    </div>

                    <!-- Services -->
                    <div class="col-lg-3 col-md-6">
                        <h5 class="footer-title">Dịch Vụ</h5>
                        <ul class="footer-links">
                            <li><a href="#"><i class="fas fa-chevron-right me-1"></i> Sân 5 người</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-1"></i> Sân 7 người</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-1"></i> Sân 11 người</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-1"></i> Tổ chức giải đấu</a></li>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div class="col-lg-3 col-md-6">
                        <h5 class="footer-title">Liên Hệ</h5>
                        <ul class="footer-contact">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>123 Nguyễn Văn Linh, Quận 7, TP.HCM</span>
                            </li>
                            <li>
                                <i class="fas fa-phone-alt"></i>
                                <span>0909 123 456</span>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <span>info@playgroundx.vn</span>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span>06:00 - 23:00 (Hàng ngày)</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <hr class="footer-divider">

                <div class="footer-bottom">
                    <p>&copy; {{ date('Y') }} PlayGroundX. Tất cả quyền được bảo lưu.</p>
                    <p>Thiết kế với <i class="fas fa-heart text-danger"></i> tại Việt Nam</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
