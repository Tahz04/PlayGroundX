<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PlayGroundX - Hệ thống đặt sân bóng đá trực tuyến hàng đầu Việt Nam. Đặt sân nhanh chóng, tiện lợi với giá tốt nhất.">
    <meta name="keywords" content="đặt sân bóng, sân bóng đá, thuê sân bóng, đặt sân online">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'PlayGroundX - Hệ Thống Đặt Sân Bóng Đá Trực Tuyến')</title>

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
<body class="{{ request()->is('ban-do*') ? 'page-map' : (request()->routeIs('profile') ? 'page-profile' : '') }}">
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
                        <a class="nav-link {{ request()->is('ban-do') ? 'active' : '' }}" href="{{ route('map') }}">
                            <i class="fas fa-map-marked-alt me-1"></i> Bản Đồ
                        </a>
                    </li>
                    <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('arenas.index') ? 'active' : '' }}" href="{{ route('arenas.index') }}">
                        <i class="fas fa-futbol me-1 d-lg-none d-xl-inline"></i> Sân Bóng
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
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-nav me-2" id="btnLogin">
                            <i class="fas fa-sign-in-alt me-1"></i> Đăng Nhập
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-accent btn-nav" id="btnRegister">
                            <i class="fas fa-user-plus me-1"></i> Đăng Ký
                        </a>
                    @endguest

                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-light btn-nav dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="userDropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" style="background: rgba(15,23,42,0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md);">
                                @if(Auth::user()->isAdmin())
                                    <span class="dropdown-item-text" style="color: #fbbf24; font-size: 0.85rem;">
                                        <i class="fas fa-user-shield me-1"></i> Quản trị viên
                                    </span>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.arenas.index') }}" style="color: rgba(255,255,255,0.7);">
                                            <i class="fas fa-cog me-2"></i> Quản Lý Sân
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.bookings.index') }}" style="color: rgba(255,255,255,0.7);">
                                            <i class="fas fa-clipboard-list me-2"></i> Quản Lý Đơn Đặt
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.1);"></li>
                                @endif
                                <li>
                                    <span class="dropdown-item-text" style="color: var(--clr-primary-400); font-size: 0.85rem;">
                                        <i class="fas fa-envelope me-1"></i> {{ Auth::user()->email }}
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.1);"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile') }}" style="color: rgba(255,255,255,0.7);">
                                        <i class="fas fa-user me-2"></i> Tài Khoản
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('bookings.my-bookings') }}" style="color: rgba(255,255,255,0.7);">
                                        <i class="fas fa-calendar-alt me-2"></i> Lịch Đặt Sân
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.1);"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('bookings.my-bookings') }}" style="color: rgba(255,255,255,0.7);">
                                        <i class="fas fa-calendar-alt me-2"></i> Lịch Sử Đặt Sân
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.1);"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Đăng Xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <!-- Toast Notification -->
        @if (session('success'))
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 80px;">
                <div class="toast show align-items-center border-0" role="alert" style="background: rgba(16,185,129,0.95); color: #fff; border-radius: var(--radius-md); backdrop-filter: blur(10px);">
                    <div class="d-flex">
                        <div class="toast-body" style="font-weight: 600;">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
            <script>
                setTimeout(function() {
                    document.querySelector('.toast')?.classList.remove('show');
                }, 4000);
            </script>
        @endif

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
