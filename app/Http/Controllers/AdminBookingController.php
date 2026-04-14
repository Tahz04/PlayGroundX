<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'arena', 'timeSlot'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.bookings.index', compact('bookings'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $booking->update(['status' => $request->status]);

        return back()->with('success', 'Đã cập nhật trạng thái đơn đặt sân.');
    }
}
