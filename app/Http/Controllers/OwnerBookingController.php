<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Arena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerBookingController extends Controller
{
    /**
     * Hiển thị danh sách đơn đặt sân của owner
     */
    public function index()
    {
        $arenaIds = Auth::user()->arenas()->pluck('id');
        
        $bookings = Booking::with(['arena', 'user', 'timeSlot', 'payment'])
            ->whereIn('arena_id', $arenaIds)
            ->orderByRaw('CASE WHEN start_time IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('owner.bookings.index', compact('bookings'));
    }

    /**
     * Cập nhật trạng thái đơn đặt sân (dùng chung cho confirm/cancel)
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $user = Auth::user();
        
        // Ensure relationships are loaded
        if (!$user->relationLoaded('arenas')) {
            $user->load('arenas');
        }
        
        // Kiểm tra booking thuộc sân của owner
        $arenaIds = $user->arenas()->pluck('id');
        if (!$arenaIds->contains($booking->arena_id)) {
            abort(403, 'Bạn không có quyền thao tác trên đơn đặt này');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        $booking->update(['status' => $request->status]);

        // Cập nhật trạng thái thanh toán tương ứng
        if ($booking->payment) {
            $paymentStatus = $request->status === 'confirmed' ? 'paid' : 'pending';
            if ($request->status === 'cancelled') {
                $paymentStatus = 'failed';
            }
            $booking->payment->update(['status' => $paymentStatus]);
        }

        $statusText = $request->status === 'confirmed' ? 'xác nhận' : ($request->status === 'cancelled' ? 'hủy' : 'cập nhật');
        return back()->with('success', "Đã {$statusText} đơn đặt sân thành công!");
    }

    public function confirm(Booking $booking)
    {
        $user = Auth::user();
        
        // Ensure relationships are loaded
        if (!$user->relationLoaded('arenas')) {
            $user->load('arenas');
        }
        
        $arenaIds = $user->arenas()->pluck('id');
        if (!$arenaIds->contains($booking->arena_id)) {
            abort(403, 'Bạn không có quyền thao tác trên đơn đặt này');
        }
        
        $booking->update(['status' => 'confirmed']);
        
        if ($booking->payment) {
            $booking->payment->update(['status' => 'paid']);
        }

        return back()->with('success', 'Đã xác nhận đơn đặt sân thành công!');
    }

    /**
     * Hủy đơn đặt sân
     */
    public function cancel(Booking $booking)
    {
        $user = Auth::user();
        
        // Ensure relationships are loaded
        if (!$user->relationLoaded('arenas')) {
            $user->load('arenas');
        }
        
        $arenaIds = $user->arenas()->pluck('id');
        if (!$arenaIds->contains($booking->arena_id)) {
            abort(403, 'Bạn không có quyền thao tác trên đơn đặt này');
        }
        
        $booking->update(['status' => 'cancelled']);
        
        if ($booking->payment) {
            $booking->payment->update(['status' => 'failed']);
        }

        return back()->with('success', 'Đã hủy đơn đặt sân.');
    }

    /**
     * Private method kiểm tra quyền owner
     */
    private function authorizeOwner($arena)
    {
        if ($arena->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền thao tác trên sân này');
        }
    }
}