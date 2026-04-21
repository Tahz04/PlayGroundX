<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerBookingController extends Controller
{
    public function index()
    {
        $arenaIds = Auth::user()->arenas()->pluck('id');
        
        $bookings = Booking::with(['arena', 'user', 'timeSlot', 'payment'])
            ->whereIn('arena_id', $arenaIds)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('owner.bookings.index', compact('bookings'));
    }

    public function confirm(Booking $booking)
    {
        $this->authorizeOwner($booking->arena);
        
        $booking->update(['status' => 'confirmed']);
        
        if ($booking->payment) {
            $booking->payment->update(['status' => 'paid']);
        }

        return back()->with('success', 'Đã xác nhận đơn đặt sân thành công!');
    }

    public function cancel(Booking $booking)
    {
        $this->authorizeOwner($booking->arena);
        
        $booking->update(['status' => 'cancelled']);
        
        if ($booking->payment) {
            $booking->payment->update(['status' => 'failed']);
        }

        return back()->with('success', 'Đã hủy đơn đặt sân.');
    }

    private function authorizeOwner($arena)
    {
        if ($arena->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
