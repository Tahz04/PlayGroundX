@extends('layouts.app')

@section('title', 'Quản Lý Đơn Đặt Sân - Admin')

@section('content')
<div class="container" style="margin-top: 100px; margin-bottom: 50px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="fas fa-clipboard-list text-primary me-2"></i>Quản Lý Đơn Đặt Sân</h2>
            <p class="text-muted">Xem và xử lý các yêu cầu đặt sân từ người dùng</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(16,185,129,0.1);">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Khách Hàng</th>
                        <th>Sân Bóng</th>
                        <th>Thời Gian</th>
                        <th>Trạng Thái</th>
                        <th class="text-end pe-4">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $booking->user->name }}</div>
                                <div class="small text-muted">{{ $booking->user->email }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $booking->arena->name }}</div>
                                <div class="small text-primary fw-semibold">{{ $booking->arena->type }}</div>
                            </td>
                            <td>
                                <div><i class="far fa-calendar-alt me-1"></i>{{ date('d/m/Y', strtotime($booking->date)) }}</div>
                                <div class="fw-bold text-primary small"><i class="far fa-clock me-1"></i>
                                    @php
                                        $timeDisplay = 'N/A';
                                        $hasNewFormat = isset($booking->start_time, $booking->end_time) && 
                                                       !is_null($booking->start_time) && 
                                                       !is_null($booking->end_time) && 
                                                       $booking->start_time !== '' && 
                                                       $booking->end_time !== '';
                                        
                                        if ($hasNewFormat) {
                                            try {
                                                $start = \Carbon\Carbon::createFromFormat('H:i:s', $booking->start_time)->format('H:i');
                                                $end = \Carbon\Carbon::createFromFormat('H:i:s', $booking->end_time)->format('H:i');
                                                $timeDisplay = $start . ' - ' . $end;
                                            } catch (\Exception $e) {
                                                $timeDisplay = 'Error';
                                            }
                                        } elseif ($booking->timeSlot) {
                                            $timeDisplay = $booking->timeSlot->formattedTime();
                                        }
                                    @endphp
                                    {{ $timeDisplay }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = [
                                        'pending' => 'bg-warning text-dark',
                                        'confirmed' => 'bg-success text-white',
                                        'cancelled' => 'bg-danger text-white',
                                        'completed' => 'bg-info text-white'
                                    ][$booking->status] ?? 'bg-secondary text-white';
                                @endphp
                                <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 small">{{ $booking->status }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <div class="input-group input-group-sm">
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Chờ</option>
                                            <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Xác nhận</option>
                                            <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Xong</option>
                                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Hủy</option>
                                        </select>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Chưa có đơn đặt sân nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
