<?php
// Test authorization logic locally

echo "=== OWNER AUTHORIZATION TEST ===\n\n";

// Simulate the authorization check
$userId = 1;  // owner user
$userArenaIds = [1];  // owner has arena 1
$bookingArenaId = 1;  // booking is for arena 1

echo "User ID: $userId\n";
echo "User's Arena IDs: " . implode(', ', $userArenaIds) . "\n";
echo "Booking Arena ID: $bookingArenaId\n\n";

// Test using in_array (PHP array check)
$hasPermission = in_array($bookingArenaId, $userArenaIds);
echo "Authorization (PHP in_array): " . ($hasPermission ? 'PASS' : 'FAIL') . "\n\n";

// The issue might be:
// 1. Auth::user()->arenas() returns empty collection
// 2. Booking->arena_id is NULL
// 3. User is not loaded correctly in middleware

echo "Possible Issues:\n";
echo "1. Check if Auth::user() is loaded correctly\n";
echo "2. Check if Auth::user()->arenas() relationship is working\n";
echo "3. Check if booking->arena_id is set (not NULL)\n";
echo "4. Check if middleware 'role:owner' is actually checking auth\n";
