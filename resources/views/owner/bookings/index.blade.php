@extends('layouts.app')

@section('title', 'Quản Lý Đơn Đặt Sân - PlayGroundX')

@section('content')
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5 align-items-center">
            <div class="col">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill py-2 px-3 mb-3">Quản Lý Đơn Hàng</span>
                <h1 class="display-6 mb-3">Đơn Đặt Sân</h1>
                <p class="text-muted">Xem và quản lý tất cả yêu cầu đặt sân của khách hàng.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Khách hàng</th>
                            <th>Sân</th>
                            <th>Thời gian</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th class="text-end pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder me-3">{{ substr($booking->user->name, 0, 1) }}</div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $booking->user->name }}</div>
                                            <div class="text-muted small">{{ $booking->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $booking->arena->name }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ date('d/m/Y', strtotime($booking->date)) }}</div>
                                    <div class="text-muted small">{{ $booking->timeSlot->formattedTime() }}</div>
                                </td>
                                <td>
                                    @if($booking->payment)
                                        <div class="fw-bold text-primary">{{ number_format($booking->payment->amount) }}đ</div>
                                        <div class="small">
                                            @if($booking->payment->method === 'bank_transfer')
                                                <span class="badge bg-info text-dark" style="font-size: 0.65rem;">Chuyển khoản</span>
                                            @else
                                                <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Tiền mặt</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">Chưa thanh toán</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($booking->status) {
                                            'pending' => 'bg-warning text-dark',
                                            'confirmed' => 'bg-success',
                                            'paid' => 'bg-primary',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        $statusText = match($booking->status) {
                                            'pending' => 'Chờ xác nhận',
                                            'confirmed' => 'Đã xác nhận',
                                            'paid' => 'Đã thanh toán',
                                            'cancelled' => 'Đã hủy',
                                            default => $booking->status
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }} rounded-pill px-3">{{ $statusText }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if($booking->status === 'pending')
                                            {{-- Nút xác nhận --}}
                                            <form action="{{ route('owner.bookings.confirm', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" onclick="return confirm('Xác nhận đơn đặt sân này?')">
                                                    <i class="fas fa-check me-1"></i> Xác nhận
                                                </button>
                                            </form>
                                            
                                            {{-- Nút hủy --}}
                                            <form action="{{ route('owner.bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Hủy đơn đặt sân này?')">
                                                    <i class="fas fa-times me-1"></i> Hủy
                                                </button>
                                            </form>
                                        @elseif($booking->status === 'confirmed')
                                            {{-- Dropdown cập nhật trạng thái (giữ từ code thứ 2) --}}
                                            <form action="{{ route('owner.bookings.update-status', $booking) }}" method="POST" class="d-flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" class="form-select form-select-sm rounded-pill w-auto" style="font-size: 0.8rem;">
                                                    <option value="pending" {{ $booking->status=='pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                                    <option value="confirmed" {{ $booking->status=='confirmed' ? 'selected' : '' }}>Xác nhận</option>
                                                    <option value="cancelled" {{ $booking->status=='cancelled' ? 'selected' : '' }}>Hủy</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">
                                                    <i class="fas fa-save me-1"></i> Lưu
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small fst-italic">Không có hành động</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open display-4 d-block mb-3 opacity-25"></i>
                                    Chưa có đơn đặt sân nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bookings->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

<style>
    .avatar-placeholder {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
    }
    .table thead th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #6c757d;
        border-bottom-width: 1px;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection