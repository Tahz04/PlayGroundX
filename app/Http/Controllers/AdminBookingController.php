<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'arena', 'timeSlot', 'payment']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%");
                })->orWhereHas('arena', function ($a) use ($search) {
                    $a->where('name', 'like', "%$search%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        $bookings = $query->orderBy('date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(15)
                          ->withQueryString();

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
