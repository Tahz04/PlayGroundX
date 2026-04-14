<?php

namespace App\Http\Controllers;

use App\Models\Arena;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class OwnerDashboardController extends Controller
{
    /**
     * Display the owner dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isOwner()) {
            return redirect()->route('profile')->with('error', 'Bạn không có quyền truy cập giao diện chủ sân.');
        }

        // Get owner's arenas
        $arenas = $user->arenas()->latest()->get();
        $totalArenas = $arenas->count();
        $totalBookings = Booking::whereIn('arena_id', $arenas->pluck('id'))->count();
        $pendingBookings = Booking::whereIn('arena_id', $arenas->pluck('id'))->where('status', 'pending')->count();
        $confirmedBookings = Booking::whereIn('arena_id', $arenas->pluck('id'))->where('status', 'confirmed')->count();

        return view('owner.dashboard', compact(
            'user',
            'arenas',
            'totalArenas',
            'totalBookings',
            'pendingBookings',
            'confirmedBookings'
        ));
    }
}
