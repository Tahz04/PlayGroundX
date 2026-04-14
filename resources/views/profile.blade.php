@extends('layouts.app')

@section('title', 'Hồ sơ của tôi - PlayGroundX')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5 align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill py-2 px-3 mb-3">Hồ sơ cá nhân</span>
                <h1 class="display-6 mb-3">Quản lý tài khoản của bạn</h1>
                <p class="text-muted fs-6">Cập nhật thông tin, đổi mật khẩu và gửi yêu cầu trở thành chủ sân. Giao diện được thiết kế nhất quán với trang chủ.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-4">
                <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                    <h5 class="mb-4">Thông tin tài khoản</h5>
                    <div class="mb-4">
                        <p class="text-muted mb-1">Họ tên</p>
                        <h6>{{ $user->name }}</h6>
                    </div>
                    <div class="mb-4">
                        <p class="text-muted mb-1">Email</p>
                        <h6>{{ $user->email }}</h6>
                    </div>
                    <div class="mb-4">
                        <p class="text-muted mb-1">Vai trò hiện tại</p>
                        <h6>{{ $user->role?->name ?? 'Khách hàng' }}</h6>
                    </div>
                    <div class="mb-4">
                        <p class="text-muted mb-1">Trạng thái</p>
                        <span class="badge rounded-pill px-3 py-2 {{ $user->status === 'pending_owner' ? 'bg-warning text-dark' : 'bg-success' }}">
                            {{ $user->status === 'pending_owner' ? 'Chờ duyệt chủ sân' : 'Hoạt động' }}
                        </span>
                    </div>
                    @if($user->role && $user->role->name === 'owner')
                        <div class="alert alert-info mb-0 rounded-4">Bạn hiện đã là chủ sân.</div>
                    @endif
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Đổi mật khẩu</h5>
                        <form action="{{ route('profile.password') }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Nhập mật khẩu hiện tại">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Nhập mật khẩu mới">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Xác nhận mật khẩu mới">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-hero-primary">Cập nhật mật khẩu</button>
                        </form>
                    </div>
                </div>

                @if($canRequestOwner)
                    <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white">
                        <div class="card-body p-4">
                            <h5 class="mb-4">Trở thành chủ sân</h5>
                            <p class="text-muted">Gửi yêu cầu để chúng tôi xem xét và mở quyền quản lý sân cho bạn.</p>
                            <form action="{{ route('owner-requests.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="message" class="form-label">Lời nhắn tới quản trị viên (tùy chọn)</label>
                                    <textarea name="message" id="message" class="form-control" rows="4">{{ old('message') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-hero-primary">Trở thành chủ sân ngay bây giờ!</button>
                            </form>
                        </div>
                    </div>
                @elseif($ownerRequests->where('status', 'pending')->isNotEmpty())
                    <div class="alert alert-warning rounded-4">Bạn đã gửi yêu cầu trở thành chủ sân và đang chờ duyệt.</div>
                @endif

                @if($ownerRequests->isNotEmpty())
                    <div class="card border-0 rounded-4 shadow-sm bg-white">
                        <div class="card-body p-4">
                            <h5 class="mb-4">Lịch sử yêu cầu</h5>
                            <div class="list-group">
                                @foreach($ownerRequests as $request)
                                    <div class="list-group-item rounded-4 mb-3 border-0 bg-light">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">{{ $request->created_at->format('d/m/Y H:i') }}</small>
                                            <span class="badge rounded-pill px-3 py-2 {{ $request->status === 'pending' ? 'bg-warning text-dark' : ($request->status === 'approved' ? 'bg-success' : 'bg-danger') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>
                                        <p class="mb-0">{{ $request->message ?? 'Không có lời nhắn.' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
