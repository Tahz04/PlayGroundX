<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$app->boot();

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Booking;

// Simulate user 1 login
Auth::loginUsingId(1);

echo "=== OWNER ACCESS TEST ===\n\n";

// 1. Check current user
$user = Auth::user();
echo "Current User: " . $user->name . " (ID: " . $user->id . ")\n";
echo "User Role: " . ($user->role ? $user->role->name : 'NO ROLE') . "\n";
echo "User Role ID: " . $user->role_id . "\n\n";

// 2. Check user's arenas
$arenas = $user->arenas()->get();
echo "User's Arenas Count: " . $arenas->count() . "\n";
foreach ($arenas as $arena) {
    echo "  - Arena {$arena->id}: {$arena->name} (Owner ID: {$arena->owner_id})\n";
}
echo "\n";

// 3. Check bookings for user's arenas
$arenaIds = $user->arenas()->pluck('id');
echo "Arena IDs: " . implode(', ', $arenaIds->toArray()) . "\n\n";

$bookings = Booking::with(['arena', 'user'])
    ->whereIn('arena_id', $arenaIds)
    ->orderByRaw('CASE WHEN start_time IS NOT NULL THEN 0 ELSE 1 END')
    ->orderBy('date', 'desc')
    ->orderBy('start_time', 'desc')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

echo "Bookings for Owner's Arenas (First 5):\n";
foreach ($bookings as $booking) {
    echo "  Booking {$booking->id}:\n";
    echo "    - User: {$booking->user->name}\n";
    echo "    - Arena: {$booking->arena->name} (Arena Owner ID: {$booking->arena->owner_id})\n";
    echo "    - Status: {$booking->status}\n";
    echo "    - Date: {$booking->date}\n";
    echo "    - Start Time: " . ($booking->start_time ?? 'NULL') . "\n";
    echo "    - End Time: " . ($booking->end_time ?? 'NULL') . "\n";
    echo "\n";
}

// 4. Check authorization
echo "Authorization Check:\n";
$testBooking = $bookings->first();
if ($testBooking) {
    echo "  Testing authorization for booking {$testBooking->id}:\n";
    echo "    - Arena User ID: {$testBooking->arena->user_id}\n";
    echo "    - Auth User ID: " . Auth::id() . "\n";
    echo "    - Authorized? " . ($testBooking->arena->user_id === Auth::id() ? 'YES' : 'NO') . "\n";
}

echo "\n=== END TEST ===\n";
