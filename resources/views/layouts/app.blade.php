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
<body class="{{ request()->is('ban-do*') ? 'page-map' : (request()->routeIs('profile') ? 'page-profile' : (request()->routeIs('owner.*') ? 'page-owner' : '')) }}">
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
                        <a class="nav-link {{ request()->routeIs('arenas.available') ? 'active' : '' }}" href="{{ route('arenas.available') }}">
                            <i class="fas fa-calendar-check me-1"></i> Lịch Trống
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
                        <div class="d-flex align-items-center">
                            <!-- Notifications -->
                            <div class="dropdown me-3">
                                <a class="text-white position-relative text-decoration-none" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-bell fs-5"></i>
                                    @if(Auth::user()->unreadNotifications->count() > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                            {{ Auth::user()->unreadNotifications->count() }}
                                        </span>
                                    @endif
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                    <li><h6 class="dropdown-header fw-bold">Thông báo mới</h6></li>
                                    @forelse(Auth::user()->unreadNotifications as $notification)
                                        @php
                                            $nAction = $notification->data['action'] ?? '';
                                            $nIcon   = match($nAction) {
                                                'created', 'approved' => ['bg-success bg-opacity-10 text-success', 'fa-check'],
                                                'submitted'           => ['bg-primary bg-opacity-10 text-primary', 'fa-user-tie'],
                                                default               => ['bg-danger bg-opacity-10 text-danger', 'fa-times'],
                                            };
                                        @endphp
                                        <li>
                                            <a class="dropdown-item py-2 border-bottom" href="{{ $notification->data['url'] ?? '#' }}">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="{{ $nIcon[0] }} rounded-circle p-2 flex-shrink-0">
                                                        <i class="fas {{ $nIcon[1] }}"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 text-wrap fw-semibold" style="font-size: 0.82rem;">{!! $notification->data['message'] !!}</p>
                                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li><span class="dropdown-item text-muted text-center py-3">Không có thông báo mới</span></li>
                                    @endforelse
                                    @if(Auth::user()->unreadNotifications->count() > 0)
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-center text-primary fw-bold pb-2">Đánh dấu tất cả đã đọc</button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <!-- User Dropdown -->
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
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.owner-requests.index') }}" style="color: rgba(255,255,255,0.7);">
                                            <i class="fas fa-user-check me-2"></i> Yêu Cầu Chủ Sân
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
                                @if(Auth::user()->isOwner())
                                    <li><hr class="dropdown-divider" style="border-color: rgba(255,255,255,0.1);"></li>
                                    <li>
                                        <span class="dropdown-item-text" style="color: #10b981; font-size: 0.85rem;">
                                            <i class="fas fa-crown me-1"></i> Chủ Sân
                                        </span>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('owner.dashboard') }}" style="color: rgba(255,255,255,0.7);">
                                            <i class="fas fa-chart-line me-2"></i> Bảng Điều Khiển
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('owner.arenas.index') }}" style="color: rgba(255,255,255,0.7);">
                                            <i class="fas fa-futbol me-2"></i> Quản Lý Sân
                                        </a>
                                    </li>
                                @endif
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
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== MAINTENANCE MODAL (global) ===== -->
    <div class="modal fade" id="maintenanceModal" tabindex="-1" aria-labelledby="maintenanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content mtn-modal">
                <!-- Header stripes -->
                <div class="mtn-stripe"></div>

                <div class="modal-body text-center px-4 pt-4 pb-2">
                    <!-- Animated icon -->
                    <div class="mtn-icon-wrap">
                        <div class="mtn-icon-ring"></div>
                        <div class="mtn-icon-ring mtn-ring-2"></div>
                        <div class="mtn-icon">
                            <i class="fas fa-wrench"></i>
                        </div>
                    </div>

                    <h4 class="mtn-title mt-4 mb-2">Sân đang bảo trì</h4>
                    <p class="mtn-arena-name mb-1" id="maintenanceArenaName"></p>
                    <p class="mtn-desc">Sân này đang được bảo trì và tạm thời không nhận đặt sân.<br>Vui lòng thử lại sau hoặc chọn một sân khác.</p>

                    <!-- Status bar -->
                    <div class="mtn-status-bar">
                        <span class="mtn-status-dot"></span>
                        <span>Dự kiến hoạt động trở lại sớm</span>
                    </div>
                </div>

                <div class="modal-footer justify-content-center border-0 pb-4 gap-2">
                    <button type="button" class="btn mtn-btn-close" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Đóng
                    </button>
                    <a href="{{ route('arenas.index') }}" class="btn mtn-btn-other">
                        <i class="fas fa-search me-1"></i>Xem sân khác
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
    .mtn-modal {
        background: #0f172a;
        border: 1px solid rgba(251,191,36,.2);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0,0,0,.6), 0 0 0 1px rgba(251,191,36,.1);
    }
    .mtn-stripe {
        height: 4px;
        background: repeating-linear-gradient(
            90deg,
            #f59e0b 0, #f59e0b 12px,
            #1e293b 12px, #1e293b 20px
        );
    }
    .mtn-icon-wrap {
        position: relative;
        width: 90px;
        height: 90px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .mtn-icon-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px solid rgba(251,191,36,.3);
        animation: mtn-ring-pulse 2s ease-out infinite;
    }
    .mtn-ring-2 {
        animation-delay: .7s;
        border-color: rgba(251,191,36,.15);
    }
    @keyframes mtn-ring-pulse {
        0%   { transform: scale(1);   opacity: 1; }
        100% { transform: scale(1.8); opacity: 0; }
    }
    .mtn-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, #92400e, #d97706);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: #fef3c7;
        box-shadow: 0 8px 24px rgba(217,119,6,.4);
        animation: mtn-wrench-shake 2.4s ease-in-out infinite;
    }
    @keyframes mtn-wrench-shake {
        0%,100% { transform: rotate(-10deg); }
        50%     { transform: rotate(10deg); }
    }
    .mtn-title {
        color: #fff;
        font-size: 1.3rem;
        font-weight: 700;
    }
    .mtn-arena-name {
        color: #fbbf24;
        font-weight: 700;
        font-size: 1rem;
    }
    .mtn-desc {
        color: rgba(255,255,255,.55);
        font-size: .875rem;
        line-height: 1.6;
    }
    .mtn-status-bar {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: rgba(251,191,36,.08);
        border: 1px solid rgba(251,191,36,.2);
        border-radius: 20px;
        padding: .4rem 1rem;
        font-size: .78rem;
        color: #fbbf24;
        font-weight: 500;
        margin-top: .5rem;
    }
    .mtn-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #f59e0b;
        animation: mtn-dot-blink 1.2s ease-in-out infinite;
        flex-shrink: 0;
    }
    @keyframes mtn-dot-blink {
        0%,100% { opacity: 1; }
        50%     { opacity: .3; }
    }
    .mtn-btn-close {
        background: rgba(255,255,255,.07);
        border: 1px solid rgba(255,255,255,.12);
        color: rgba(255,255,255,.7);
        border-radius: 10px;
        font-size: .875rem;
        padding: .5rem 1.25rem;
        transition: all .2s;
    }
    .mtn-btn-close:hover { background: rgba(255,255,255,.12); color: #fff; }
    .mtn-btn-other {
        background: linear-gradient(135deg, #d97706, #f59e0b);
        color: #1a1a1a !important;
        border: none;
        border-radius: 10px;
        font-size: .875rem;
        font-weight: 700;
        padding: .5rem 1.25rem;
        transition: all .2s;
    }
    .mtn-btn-other:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(217,119,6,.4); }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('maintenanceModal');
        if (modal) {
            modal.addEventListener('show.bs.modal', function (e) {
                var btn = e.relatedTarget;
                var name = btn ? btn.getAttribute('data-arena-name') : '';
                document.getElementById('maintenanceArenaName').textContent = name ? '"' + name + '"' : '';
            });
        }
    });
    </script>

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
    <!-- Chatbot Widget -->
    <div id="chatbot-widget" class="chatbot-widget">
        <button id="chatbot-toggle" class="chatbot-toggle">
            <i class="fas fa-comment-dots"></i>
        </button>
        <div id="chatbot-window" class="chatbot-window hidden">
            <div class="chatbot-header">
                <div class="d-flex align-items-center">
                    <div class="chatbot-avatar"><i class="fas fa-robot"></i></div>
                    <div class="ms-2">
                        <h6 class="mb-0 fw-bold">Trợ Lý PlayGroundX</h6>
                        <small class="text-white-50">Sẵn sàng hỗ trợ</small>
                    </div>
                </div>
                <button id="chatbot-close" class="btn-close btn-close-white"></button>
            </div>
            <div id="chatbot-messages" class="chatbot-messages">
                <!-- Messages will be injected here -->
            </div>
            <div class="chatbot-footer">
                <small class="text-muted d-block text-center"><i class="fas fa-bolt text-warning"></i> Được hỗ trợ bởi PlayGroundX AI</small>
            </div>
        </div>
    </div>

    <style>
        .chatbot-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            font-family: 'Inter', sans-serif;
        }
        .chatbot-toggle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--clr-primary-400), var(--clr-primary-500));
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
            font-size: 28px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .chatbot-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6);
        }
        .chatbot-window {
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 350px;
            height: 500px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: all 0.3s ease;
            transform-origin: bottom right;
        }
        .chatbot-window.hidden {
            transform: scale(0);
            opacity: 0;
            pointer-events: none;
        }
        .chatbot-header {
            background: linear-gradient(135deg, var(--clr-dark-900), var(--clr-dark-800));
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chatbot-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: var(--clr-primary-400);
        }
        .chatbot-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .chat-bubble {
            max-width: 85%;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 0.95rem;
            line-height: 1.5;
            position: relative;
            animation: fadeIn 0.3s ease;
        }
        .chat-bubble.bot {
            background: white;
            color: var(--clr-dark-800);
            border: 1px solid #e2e8f0;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }
        .chat-bubble.user {
            background: var(--clr-primary-500);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }
        .chat-options {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 5px;
        }
        .chat-btn {
            background: white;
            border: 1px solid var(--clr-primary-400);
            color: var(--clr-primary-500);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            text-align: left;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }
        .chat-btn:hover {
            background: var(--clr-primary-50);
        }
        .chatbot-footer {
            padding: 10px;
            background: white;
            border-top: 1px solid #e2e8f0;
        }
        .typing-indicator {
            display: inline-flex;
            gap: 4px;
            padding: 5px 10px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            width: fit-content;
        }
        .typing-dot {
            width: 6px;
            height: 6px;
            background: var(--clr-dark-300);
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out both;
        }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('chatbot-toggle');
            const closeBtn = document.getElementById('chatbot-close');
            const chatWindow = document.getElementById('chatbot-window');
            const messagesContainer = document.getElementById('chatbot-messages');
            let isFirstLoad = true;

            toggleBtn.addEventListener('click', () => {
                chatWindow.classList.toggle('hidden');
                if(isFirstLoad && !chatWindow.classList.contains('hidden')) {
                    sendAction('hello');
                    isFirstLoad = false;
                }
            });

            closeBtn.addEventListener('click', () => {
                chatWindow.classList.add('hidden');
            });

            function showTyping() {
                const id = 'typing-' + Date.now();
                const html = `<div id="${id}" class="typing-indicator"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>`;
                messagesContainer.insertAdjacentHTML('beforeend', html);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                return id;
            }

            function removeTyping(id) {
                const el = document.getElementById(id);
                if(el) el.remove();
            }

            function addMessage(text, type) {
                const html = `<div class="chat-bubble ${type}">${text}</div>`;
                messagesContainer.insertAdjacentHTML('beforeend', html);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            function addOptions(options) {
                if(!options || options.length === 0) return;
                let html = '<div class="chat-options">';
                options.forEach(opt => {
                    html += `<button class="chat-btn" data-action="${opt.action}" ${opt.url ? `data-url="${opt.url}"` : ''}>${opt.text}</button>`;
                });
                html += '</div>';
                messagesContainer.insertAdjacentHTML('beforeend', html);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;

                // Add event listeners to new buttons
                const buttons = messagesContainer.querySelectorAll('.chat-options:last-child .chat-btn');
                buttons.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const url = this.getAttribute('data-url');
                        if (url) {
                            window.location.href = url;
                            return;
                        }
                        
                        const action = this.getAttribute('data-action');
                        const text = this.innerText;
                        
                        // Add user message
                        addMessage(text, 'user');
                        
                        // Remove options
                        this.parentElement.remove();
                        
                        // Send request
                        sendAction(action);
                    });
                });
            }

            function sendAction(action) {
                const typingId = showTyping();
                
                fetch('{{ route('chatbot.handle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ action: action })
                })
                .then(res => res.json())
                .then(data => {
                    setTimeout(() => {
                        removeTyping(typingId);
                        if (data.messages && data.messages.length > 0) {
                            data.messages.forEach(msg => addMessage(msg, 'bot'));
                        }
                        if (data.options) {
                            addOptions(data.options);
                        }
                    }, 600); // Fake delay for typing feel
                })
                .catch(err => {
                    removeTyping(typingId);
                    addMessage('Xin lỗi, hệ thống chat đang bận. Vui lòng thử lại sau!', 'bot');
                });
            }
        });
    </script>
</body>
</html>
