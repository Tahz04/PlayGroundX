<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
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

        // Lọc theo period (day/month/year)
        $period = request('period', 'month');
        $rawDate = request('date', '');
        
        // Normalize selectedDate dựa theo period
        $selectedDate = '';
        switch ($period) {
            case 'day':
                // Nếu là Y-m hoặc Y, convert sang Y-m-d
                if (strlen($rawDate) == 7) { // Y-m format
                    $selectedDate = substr($rawDate, 0, 7) . '-01';
                } elseif (strlen($rawDate) == 4) { // Y format
                    $selectedDate = $rawDate . '-01-01';
                } else {
                    $selectedDate = $rawDate ?: now()->format('Y-m-d');
                }
                break;
                
            case 'month':
                // Nếu là Y-m-d, lấy năm-tháng; nếu là Y, convert sang Y-01
                if (strlen($rawDate) == 10) { // Y-m-d format
                    $selectedDate = substr($rawDate, 0, 7);
                } elseif (strlen($rawDate) == 4) { // Y format
                    $selectedDate = $rawDate . '-01';
                } else {
                    $selectedDate = $rawDate ?: now()->format('Y-m');
                }
                break;
                
            case 'year':
                // Lấy chỉ năm
                if (strlen($rawDate) >= 4) {
                    $selectedDate = substr($rawDate, 0, 4);
                } else {
                    $selectedDate = $rawDate ?: now()->format('Y');
                }
                break;
                
            default:
                $selectedDate = $rawDate ?: now()->format('Y-m');
        }
        
        // Tính from/to date dựa theo period
        $fromDate = $toDate = null;
        $displayFormat = '';
        
        switch ($period) {
            case 'day':
                // Input: Y-m-d format
                $date = Carbon::createFromFormat('Y-m-d', $selectedDate);
                $fromDate = $date->copy()->startOfDay();
                $toDate = $date->copy()->endOfDay();
                $displayFormat = $date->locale('vi')->isoFormat('D MMMM Y');
                break;
            
            case 'year':
                // Input: YYYY format (only year number)
                $year = (int)$selectedDate;
                $fromDate = Carbon::createFromFormat('Y-m-d', "$year-01-01")->startOfDay();
                $toDate = Carbon::createFromFormat('Y-m-d', "$year-12-31")->endOfDay();
                $displayFormat = "Năm $year";
                break;
            
            case 'month':
            default:
                // Input: Y-m format
                $date = Carbon::createFromFormat('Y-m', $selectedDate)->startOfMonth();
                $fromDate = $date->copy()->startOfDay();
                $toDate = $date->copy()->endOfMonth()->endOfDay();
                $displayFormat = $date->locale('vi')->isoFormat('MMMM Y');
                break;
        }

        // Tính doanh thu theo period
        $income = Payment::whereHas('booking', function ($q) use ($arenaIds, $fromDate, $toDate) {
                $q->whereIn('arena_id', $arenaIds)
                  ->whereIn('status', ['confirmed', 'paid', 'completed'])
                  ->whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()]);
            })
            ->where('status', 'paid')
            ->sum('amount');

        $incomeByArena = Payment::selectRaw('bookings.arena_id, SUM(payments.amount) as total')
            ->join('bookings', 'bookings.id', '=', 'payments.booking_id')
            ->whereIn('bookings.arena_id', $arenaIds)
            ->whereIn('bookings.status', ['confirmed', 'paid', 'completed'])
            ->whereBetween('bookings.date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->where('payments.status', 'paid')
            ->groupBy('bookings.arena_id')
            ->get()
            ->keyBy('arena_id');

        return view('owner.dashboard', compact(
            'user', 'arenas', 'totalArenas', 'totalBookings',
            'pendingBookings', 'confirmedBookings',
            'income', 'incomeByArena', 'period', 'selectedDate', 'displayFormat'
        ));
    }
}
