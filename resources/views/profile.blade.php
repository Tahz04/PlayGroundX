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
                        <span class="badge rounded-pill px-3 py-2 {{ $user->role && $user->role->name === 'owner' ? 'bg-primary text-white' : ($user->status === 'pending_owner' ? 'bg-warning text-dark' : 'bg-success') }}">
                            {{ $user->role && $user->role->name === 'owner' ? 'Chủ sân' : ($user->status === 'pending_owner' ? 'Chờ duyệt chủ sân' : 'Hoạt động') }}
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
                            <h5 class="mb-2">Trở thành chủ sân</h5>
                            <p class="text-muted mb-4">Gửi yêu cầu kèm giấy tờ tùy thân để chúng tôi xác minh và mở quyền quản lý sân cho bạn.</p>

                            @if(session('success'))
                                <div class="alert alert-success rounded-3 mb-3">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger rounded-3 mb-3">{{ session('error') }}</div>
                            @endif

                            <form action="{{ route('owner-requests.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-id-card text-primary me-1"></i>
                                        Ảnh giấy tờ mặt trước <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="image_1"
                                           class="form-control @error('image_1') is-invalid @enderror"
                                           accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="form-text">CMND/CCCD/Hộ chiếu mặt trước. JPG, PNG hoặc PDF, tối đa 2MB.</div>
                                    @error('image_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-id-card-alt text-primary me-1"></i>
                                        Ảnh giấy tờ mặt sau <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" name="image_2"
                                           class="form-control @error('image_2') is-invalid @enderror"
                                           accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="form-text">CMND/CCCD/Hộ chiếu mặt sau. JPG, PNG hoặc PDF, tối đa 2MB.</div>
                                    @error('image_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="message" class="form-label">Lời nhắn tới quản trị viên <span class="text-muted">(tùy chọn)</span></label>
                                    <textarea name="message" id="message" class="form-control" rows="3"
                                              placeholder="Thông tin thêm về sân bạn muốn quản lý...">{{ old('message') }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-hero-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif($ownerRequests->where('status', 'pending')->isNotEmpty())
                    <div class="alert alert-warning rounded-4">Bạn đã gửi yêu cầu trở thành chủ sân và đang chờ duyệt.</div>
                @endif

                @if($user->role && $user->role->name === 'owner')
                    <div class="card border-0 rounded-4 shadow-sm mb-4 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h5 class="mb-1">Giao diện chủ sân</h5>
                                    <p class="text-muted mb-0">Bạn đã là chủ sân, hãy sử dụng bảng điều khiển để quản lý sân và theo dõi đơn đặt.</p>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill py-2 px-3">Chủ sân</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('owner.dashboard') }}" class="btn btn-hero-primary">Bảng Điều Khiển</a>
                                <a href="{{ route('owner.arenas.index') }}" class="btn btn-hero-primary">Quản Lý Sân</a>
                            </div>
                        </div>
                    </div>
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
