<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Arena;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Show the booking form/page for a specific arena.
     */
    public function create(Arena $arena)
    {
        $timeSlots = TimeSlot::all();
        return view('bookings.create', compact('arena', 'timeSlots'));
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'arena_id' => 'required|exists:arenas,id',
            'date' => 'required|date|after_or_equal:today',
            'time_slot_id' => 'required|exists:time_slots,id',
        ]);

        // Check if already booked
        $exists = Booking::where('arena_id', $request->arena_id)
            ->where('date', $request->date)
            ->where('time_slot_id', $request->time_slot_id)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Khung giờ này đã có người đặt. Vui lòng chọn giờ khác.');
        }

        Booking::create([
            'user_id' => Auth::id(),
            'arena_id' => $request->arena_id,
            'date' => $request->date,
            'time_slot_id' => $request->time_slot_id,
            'status' => 'pending',
        ]);

        return redirect()->route('home')->with('success', 'Đã gửi yêu cầu đặt sân thành công! Vui lòng chờ admin xác nhận.');
    }

    /**
     * Display the user's bookings.
     */
    public function myBookings()
    {
        $bookings = Booking::with(['arena', 'timeSlot'])
            ->where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();
            
        return view('bookings.my-bookings', compact('bookings'));
    }
}
