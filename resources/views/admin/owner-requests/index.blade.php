@extends('layouts.app')

@section('title', 'Quản lý yêu cầu chủ sân')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Yêu cầu trở thành chủ sân</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-borderless align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Người dùng</th>
                    <th>Email</th>
                    <th>Lời nhắn</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ $request->user->email }}</td>
                        <td>{{ $request->message ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $request->status === 'pending' ? 'warning text-dark' : ($request->status === 'approved' ? 'success' : 'danger') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($request->status === 'pending')
                                <form action="{{ route('admin.owner-requests.approve', $request) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                </form>
                                <form action="{{ route('admin.owner-requests.reject', $request) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-danger">Từ chối</button>
                                </form>
                            @else
                                <span class="text-muted">Đã xử lý</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Chưa có yêu cầu nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
