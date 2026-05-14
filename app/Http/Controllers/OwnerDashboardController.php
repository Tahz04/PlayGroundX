<?php

namespace App\Http\Controllers;

use App\Models\Arena;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->isOwner()) {
            return redirect()->route('profile')->with('error', 'Bạn không có quyền truy cập giao diện chủ sân.');
        }

        $arenas    = $user->arenas()->latest()->get();
        $arenaIds  = $arenas->pluck('id');

        $totalArenas       = $arenas->count();
        $totalBookings     = Booking::whereIn('arena_id', $arenaIds)->count();
        $pendingBookings   = Booking::whereIn('arena_id', $arenaIds)->where('status', 'pending')->count();
        $confirmedBookings = Booking::whereIn('arena_id', $arenaIds)->where('status', 'confirmed')->count();

        // Thu nhập theo tháng
        $selectedMonth = request('month', now()->format('Y-m'));
        [$year, $month] = explode('-', $selectedMonth);

        $monthlyIncome = Payment::whereHas('booking', function ($q) use ($arenaIds, $year, $month) {
                $q->whereIn('arena_id', $arenaIds)
                  ->whereIn('status', ['confirmed', 'paid', 'completed'])
                  ->whereYear('date', $year)
                  ->whereMonth('date', $month);
            })
            ->where('status', 'paid')
            ->sum('amount');

        $incomeByArena = Payment::selectRaw('bookings.arena_id, SUM(payments.amount) as total')
            ->join('bookings', 'bookings.id', '=', 'payments.booking_id')
            ->whereIn('bookings.arena_id', $arenaIds)
            ->whereIn('bookings.status', ['confirmed', 'paid', 'completed'])
            ->whereYear('bookings.date', $year)
            ->whereMonth('bookings.date', $month)
            ->where('payments.status', 'paid')
            ->groupBy('bookings.arena_id')
            ->get()
            ->keyBy('arena_id');

        return view('owner.dashboard', compact(
            'user', 'arenas', 'totalArenas', 'totalBookings',
            'pendingBookings', 'confirmedBookings',
            'monthlyIncome', 'incomeByArena', 'selectedMonth'
        ));
    }
}
