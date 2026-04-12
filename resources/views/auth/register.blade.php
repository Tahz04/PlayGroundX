@extends('layouts.app')

@section('title', 'Đăng Ký - PlayGroundX')

@push('styles')
<style>
    .auth-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--clr-dark-950);
        position: relative;
        overflow: hidden;
        padding: 6rem 1rem 3rem;
    }

    .auth-page::before {
        content: '';
        position: absolute;
        top: -30%;
        left: -20%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(16,185,129,0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .auth-page::after {
        content: '';
        position: absolute;
        bottom: -20%;
        right: -15%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(245,158,11,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .auth-container {
        width: 100%;
        max-width: 520px;
        position: relative;
        z-index: 2;
    }

    .auth-card {
        background: rgba(255,255,255,0.03);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: var(--radius-2xl);
        padding: 3rem;
        box-shadow: 0 25px 60px rgba(0,0,0,0.4);
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .auth-logo {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .auth-logo .brand-icon {
        font-size: 2.5rem;
    }

    .auth-logo .brand-text {
        font-size: 1.8rem;
    }

    .auth-title {
        font-family: var(--font-display);
        font-size: 1.75rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .auth-subtitle {
        color: rgba(255,255,255,0.5);
        font-size: 0.95rem;
    }

    .auth-form .form-label {
        color: rgba(255,255,255,0.7);
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .auth-form .form-control {
        background: rgba(255,255,255,0.06);
        border: 1.5px solid rgba(255,255,255,0.1);
        color: #fff;
        padding: 0.8rem 1rem 0.8rem 2.75rem;
        border-radius: var(--radius-md);
        font-size: 0.95rem;
        transition: var(--transition-base);
    }

    .auth-form .form-control::placeholder {
        color: rgba(255,255,255,0.3);
    }

    .auth-form .form-control:focus {
        background: rgba(255,255,255,0.1);
        border-color: var(--clr-primary-500);
        box-shadow: 0 0 0 4px rgba(16,185,129,0.15);
        color: #fff;
    }

    .auth-form .input-icon-wrap .input-icon {
        color: rgba(255,255,255,0.4);
    }

    .auth-form .form-control:focus ~ .input-icon,
    .auth-form .form-control:focus + .input-icon {
        color: var(--clr-primary-400);
    }

    .auth-form .input-icon-wrap {
        position: relative;
    }

    .auth-form .input-icon-wrap .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
        transition: var(--transition-fast);
        pointer-events: none;
    }

    .btn-auth {
        width: 100%;
        padding: 0.9rem;
        background: var(--gradient-primary);
        color: #fff;
        font-weight: 700;
        font-size: 1.05rem;
        border: none;
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: var(--transition-base);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }

    .btn-auth:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-glow);
        color: #fff;
    }

    .auth-divider {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 1.5rem 0;
        color: rgba(255,255,255,0.3);
        font-size: 0.85rem;
    }

    .auth-divider::before,
    .auth-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(255,255,255,0.1);
    }

    .auth-footer {
        text-align: center;
        margin-top: 2rem;
        color: rgba(255,255,255,0.5);
        font-size: 0.95rem;
    }

    .auth-footer a {
        color: var(--clr-primary-400);
        font-weight: 600;
        transition: var(--transition-fast);
    }

    .auth-footer a:hover {
        color: var(--clr-primary-300);
        text-decoration: underline;
    }

    .mb-3-5 { margin-bottom: 1.25rem; }

    /* Alert styles */
    .alert-auth {
        background: rgba(239,68,68,0.15);
        border: 1px solid rgba(239,68,68,0.3);
        color: #fca5a5;
        border-radius: var(--radius-md);
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }

    .alert-auth ul {
        margin: 0;
        padding-left: 1.25rem;
    }

    .alert-auth li {
        margin-bottom: 0.2rem;
    }

    .alert-success-auth {
        background: rgba(16,185,129,0.15);
        border: 1px solid rgba(16,185,129,0.3);
        color: var(--clr-primary-300);
        border-radius: var(--radius-md);
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255,255,255,0.4);
        cursor: pointer;
        z-index: 3;
        padding: 0;
        transition: var(--transition-fast);
    }

    .password-toggle:hover {
        color: var(--clr-primary-400);
    }

    .is-invalid {
        border-color: #ef4444 !important;
    }
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-container" data-aos="fade-up">
        <div class="auth-card">
            <!-- Header -->
            <div class="auth-header">
                <a href="{{ route('home') }}" class="auth-logo">
                    <i class="fas fa-futbol brand-icon"></i>
                    <span class="brand-text">Play<span class="brand-highlight">Ground</span>X</span>
                </a>
                <h1 class="auth-title">Tạo Tài Khoản Mới</h1>
                <p class="auth-subtitle">Đăng ký để đặt sân nhanh chóng và nhận ưu đãi</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert-auth">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" class="auth-form" id="registerForm">
                @csrf

                <!-- Name -->
                <div class="mb-3-5">
                    <label for="name" class="form-label">Họ và Tên</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               placeholder="Nhập họ và tên của bạn"
                               required autofocus>
                    </div>
                </div>

                <!-- Email -->
                <div class="mb-3-5">
                    <label for="email" class="form-label">Địa Chỉ Email</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="example@email.com"
                               required>
                    </div>
                </div>

                <!-- Phone -->
                <div class="mb-3-5">
                    <label for="phone" class="form-label">Số Điện Thoại <span style="color: rgba(255,255,255,0.3);">(không bắt buộc)</span></label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="tel" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}" 
                               placeholder="0909 xxx xxx">
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3-5">
                    <label for="password" class="form-label">Mật Khẩu</label>
                    <div class="input-icon-wrap" style="position: relative;">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Tối thiểu 8 ký tự"
                               required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3-5">
                    <label for="password_confirmation" class="form-label">Xác Nhận Mật Khẩu</label>
                    <div class="input-icon-wrap" style="position: relative;">
                        <i class="fas fa-shield-alt input-icon"></i>
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="Nhập lại mật khẩu"
                               required>
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-auth">
                    <i class="fas fa-user-plus"></i>
                    Đăng Ký Ngay
                </button>
            </form>

            <div class="auth-divider">hoặc</div>

            <!-- Footer -->
            <div class="auth-footer">
                Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endpush
