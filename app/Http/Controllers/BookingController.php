<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Arena;
use App\Models\Payment;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Show the booking form/page for a specific arena.
     */
    public function create(Request $request, Arena $arena)
    {
        $date = $request->input('date', date('Y-m-d'));
        $timeSlots = $this->getStandardOrderedSlots();

        // Fetch booked slots for this arena on this date
        $bookings = Booking::where('arena_id', $arena->id)
            ->where('date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->get();

        $bookedSlotIds = $bookings->whereNotNull('time_slot_id')->pluck('time_slot_id')->toArray();
        
        $bookedRanges = $bookings->map(function($booking) {
            if (!empty($booking->start_time) && !empty($booking->end_time) && $booking->start_time && $booking->end_time) {
                return [
                    'start' => $booking->start_time,
                    'end' => $booking->end_time
                ];
            } elseif ($booking->timeSlot) {
                return [
                    'start' => $booking->timeSlot->start_time,
                    'end' => $booking->timeSlot->end_time
                ];
            }
            return null;
        })->filter()->values();

        return view('bookings.create', compact('arena', 'timeSlots', 'bookedSlotIds', 'bookedRanges', 'date'));
    }

    /**
     * AJAX endpoint to get booked slots for a date.
     */
    public function getBookedSlots(Request $request, Arena $arena)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        $bookings = Booking::where('arena_id', $arena->id)
            ->where('date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->get();

        $bookedRanges = [];
        
        foreach ($bookings as $booking) {
            if (!empty($booking->start_time) && !empty($booking->end_time) && $booking->start_time && $booking->end_time) {
                // New booking format with start_time and end_time
                $bookedRanges[] = [
                    'start' => $booking->start_time,
                    'end' => $booking->end_time
                ];
            } elseif ($booking->time_slot_id && $booking->timeSlot) {
                // Old booking format with time_slot_id
                $bookedRanges[] = [
                    'start' => $booking->timeSlot->start_time,
                    'end' => $booking->timeSlot->end_time
                ];
            }
        }

        // Also return old format for backward compatibility
        $bookedSlotIds = $bookings->whereNotNull('time_slot_id')->pluck('time_slot_id')->toArray();

        return response()->json([
            'bookedSlotIds' => $bookedSlotIds,
            'bookedRanges' => $bookedRanges
        ]);
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'arena_id' => 'required|exists:arenas,id',
            'date' => 'required|date|after_or_equal:today',
            'start_hour' => 'required',
            'end_hour' => 'required',
            'payment_method' => 'required|in:cash,bank_transfer',
        ], [
            'start_hour.required' => 'Vui lòng chọn giờ bắt đầu.',
            'end_hour.required' => 'Vui lòng chọn giờ kết thúc.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ]);

        // Note: start_hour and end_hour are now expected to be strings like "06:00", "06:30"
        $startTimeStr = $request->input('start_hour');
        $endTimeStr = $request->input('end_hour');
        $paymentMethod = $request->input('payment_method');

        $startMinutes = $this->timeToMinutes($startTimeStr);
        $endMinutes = $this->timeToMinutes($endTimeStr);
        
        if ($endMinutes <= $startMinutes) {
            return back()
                ->withInput()
                ->with('error', 'Giờ kết thúc phải lớn hơn giờ bắt đầu.');
        }

        $durationMinutes = $endMinutes - $startMinutes;

        if ($durationMinutes < 60) {
            return back()
                ->withInput()
                ->with('error', 'Thời gian đặt sân tối thiểu là 1 tiếng (60 phút).');
        }

        $orderedSlots = $this->getStandardOrderedSlots();

        $selectedSlots = $orderedSlots
            ->filter(function (TimeSlot $slot) use ($startTimeStr, $endTimeStr) {
                $slotStart = substr($slot->start_time, 0, 5);
                $slotEnd = $slot->end_time === '00:00:00' ? '24:00' : substr($slot->end_time, 0, 5);
                
                return $slotStart >= $startTimeStr && ($slotEnd <= $endTimeStr || ($endTimeStr === '24:00' && $slotEnd === '24:00'));
            })
            ->values();

        // Each slot is 30 mins, so expected slots is duration / 30
        $expectedSlotCount = $durationMinutes / 30;

        if ($selectedSlots->count() !== $expectedSlotCount) {
            return back()
                ->withInput()
                ->with('error', 'Khoảng giờ đã chọn chưa khả dụng hoặc bị thiếu khung giờ liên tiếp.');
        }

        $slotIds = $selectedSlots->pluck('id')->all();

        // Check if any selected slot is already booked.
        $conflictingBookings = Booking::where('arena_id', $request->arena_id)
            ->where('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->filter(function ($booking) use ($startMinutes, $endMinutes, $startTimeStr, $endTimeStr) {
                // Determine booking start and end times
                if (!empty($booking->start_time) && !empty($booking->end_time) && $booking->start_time && $booking->end_time) {
                    $bookingStart = $booking->start_time;
                    $bookingEnd = $booking->end_time;
                } elseif ($booking->timeSlot) {
                    $bookingStart = $booking->timeSlot->start_time;
                    $bookingEnd = $booking->timeSlot->end_time;
                } else {
                    return false;
                }
                
                $bookingStartMinutes = $this->timeToMinutes($bookingStart);
                $bookingEndMinutes = $this->timeToMinutes($bookingEnd);
                
                // Check if time ranges overlap
                return !($endMinutes <= $bookingStartMinutes || $startMinutes >= $bookingEndMinutes);
            });

        if ($conflictingBookings->isNotEmpty()) {
            $conflictTimes = $conflictingBookings
                ->map(function ($b) {
                    if (!empty($b->start_time) && !empty($b->end_time) && $b->start_time && $b->end_time) {
                        return substr($b->start_time, 0, 5) . ' - ' . substr($b->end_time, 0, 5);
                    } elseif ($b->timeSlot) {
                        return $b->timeSlot->formattedTime();
                    }
                    return 'Unknown';
                })
                ->implode(', ');

            return back()
                ->withInput()
                ->with('error', 'Các khung giờ sau đã có người đặt: ' . $conflictTimes . '. Vui lòng chọn giờ khác.');
        }

        $arena = Arena::findOrFail((int) $request->arena_id);
        
        // Calculate total price based on duration (Rate per Hour)
        $totalPrice = ($durationMinutes / 60) * $arena->price;

        // Apply promotion: 10% discount for bookings >= 3 hours (180 mins)
        if ($durationMinutes >= 180) {
            $totalPrice = $totalPrice * 0.9;
        }
        
        $createdBookingIds = [];

        DB::transaction(function () use ($request, $startTimeStr, $endTimeStr, $arena, $paymentMethod, $totalPrice, &$createdBookingIds) {
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'arena_id' => $request->arena_id,
                'date' => $request->date,
                'start_time' => $startTimeStr . ':00',
                'end_time' => $endTimeStr . ':00',
                'time_slot_id' => null,
                'status' => 'pending',
            ]);

            $createdBookingIds[] = $booking->id;

            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $totalPrice,
                'method' => $paymentMethod,
                'status' => $paymentMethod === 'bank_transfer' ? 'unpaid' : 'pending',
            ]);
        });

        $bookingIdsParam = implode('-', $createdBookingIds);
        $successMessage = 'Đã tạo lịch đặt từ ' . $startTimeStr . ' đến ' . $endTimeStr . ' thành công.';

        // Gửi thông báo cho Admin và Chủ sân (nếu có)
        try {
            $admins = \App\Models\User::whereHas('role', function($q) {
                $q->where('name', 'admin');
            })->get();

            $receivers = $admins;
            if ($arena->owner_id) {
                $owner = \App\Models\User::find($arena->owner_id);
                if ($owner) {
                    $receivers->push($owner);
                }
            }

            // Dùng đơn đầu tiên làm đại diện thông báo
            if (!empty($createdBookingIds)) {
                $firstBooking = Booking::find($createdBookingIds[0]);
                $msg = Auth::user()->name . ' vừa đặt sân ' . $arena->name;
                
                \Illuminate\Support\Facades\Notification::send($receivers->unique('id'), new \App\Notifications\BookingNotification(
                    $firstBooking, 
                    'created', 
                    $msg
                ));

                // Gửi thông báo Telegram cho Admin
                if (env('TELEGRAM_BOT_TOKEN')) {
                    $zaloMessage = "<b>🎉 CÓ ĐƠN ĐẶT SÂN MỚI</b>\n\n";
                    $zaloMessage .= "👤 <b>Khách hàng:</b> " . Auth::user()->name . "\n";
                    $zaloMessage .= "⚽ <b>Sân:</b> " . $arena->name . "\n";
                    $zaloMessage .= "📅 <b>Ngày:</b> " . $firstBooking->date . "\n";
                    $zaloMessage .= "⏰ <b>Giờ:</b> " . $startTimeStr . " - " . $endTimeStr . "\n";
                    $zaloMessage .= "💰 <b>Tổng tiền:</b> " . number_format($totalPrice) . " VNĐ\n";
                    
                    \App\Services\TelegramService::sendMessage($zaloMessage);
                }
            }
        } catch (\Exception $e) {
            // Log error if needed, but don't break the booking flow
        }

        if ($paymentMethod === 'bank_transfer') {
            return redirect()
                ->route('bookings.payment-transfer', ['bookings' => $bookingIdsParam])
                ->with('success', $successMessage . ' Vui lòng hoàn tất chuyển khoản để xác nhận thanh toán.');
        }

        return redirect()
            ->route('bookings.bill', ['bookings' => $bookingIdsParam])
            ->with('success', $successMessage . ' Bạn đã chọn thanh toán tiền mặt tại sân.');
    }

    /**
     * Show bill details for a created booking batch.
     */
    public function bill(Request $request)
    {
        $bookings = $this->getUserBookingsFromQuery($request);

        if ($bookings->isEmpty()) {
            return redirect()->route('bookings.my-bookings')->with('error', 'Không tìm thấy hóa đơn cần hiển thị.');
        }

        $firstBooking = $bookings->first();
        $lastBooking = $bookings->last();

        // Handle both old (with time_slot_id) and new (with start_time/end_time) bookings
        if (!empty($firstBooking->start_time) && !empty($lastBooking->end_time) && $firstBooking->start_time && $lastBooking->end_time) {
            $startClock = date('H:i', strtotime($firstBooking->start_time));
            $endClock = date('H:i', strtotime($lastBooking->end_time));
        } else {
            // Fallback to timeSlot
            $startClock = $firstBooking->timeSlot ? date('H:i', strtotime($firstBooking->timeSlot->start_time)) : 'N/A';
            $endClock = $lastBooking->timeSlot ? ($lastBooking->timeSlot->end_time === '00:00:00'
                ? '24:00'
                : date('H:i', strtotime($lastBooking->timeSlot->end_time))) : 'N/A';
        }

        $paymentMethod = $firstBooking->payment?->method ?? 'cash';
        $paymentStatus = $firstBooking->payment?->status ?? 'pending';
        $totalAmount = $bookings->sum(fn (Booking $booking) => $booking->payment?->amount ?? $booking->arena->price);
        $bookingIdsParam = $request->query('bookings');

        return view('bookings.bill', compact(
            'bookings',
            'firstBooking',
            'startClock',
            'endClock',
            'paymentMethod',
            'paymentStatus',
            'totalAmount',
            'bookingIdsParam'
        ));
    }

    /**
     * Show transfer payment instructions for a created booking batch.
     */
    public function paymentTransfer(Request $request)
    {
        $bookings = $this->getUserBookingsFromQuery($request);

        if ($bookings->isEmpty()) {
            return redirect()->route('bookings.my-bookings')->with('error', 'Không tìm thấy đơn đặt sân để thanh toán.');
        }

        $firstBooking = $bookings->first();
        $totalAmount = $bookings->sum(fn (Booking $booking) => $booking->payment?->amount ?? $booking->arena->price);
        $bookingIdsParam = $request->query('bookings');

        return view('bookings.payment-transfer', compact('bookings', 'firstBooking', 'totalAmount', 'bookingIdsParam'));
    }

    /**
     * Confirm user has paid via transfer.
     */
    public function confirmPayment(Request $request)
    {
        $bookings = $this->getUserBookingsFromQuery($request);
        
        if ($bookings->isEmpty()) {
            return back()->with('error', 'Không tìm thấy đơn đặt sân.');
        }

        foreach ($bookings as $booking) {
            if ($booking->payment && $booking->payment->status === 'unpaid') {
                $booking->payment->update(['status' => 'pending']);
            }
        }

        return redirect()->route('bookings.my-bookings')->with('success', 'Đã gửi yêu cầu xác nhận thanh toán. Vui lòng chờ admin kiểm tra.');
    }

    /**
     * Ensure standard 30-minute slots from 06:00 to 24:00 are available.
     */
    private function ensureStandardTimeSlots(): void
    {
        for ($hour = 6; $hour < 24; $hour++) {
            for ($min = 0; $min < 60; $min += 30) {
                $startTime = sprintf('%02d:%02d:00', $hour, $min);
                $endHour = ($min + 30 >= 60) ? $hour + 1 : $hour;
                $endMin = ($min + 30 >= 60) ? 0 : $min + 30;
                
                $endTime = $endHour === 24 ? '00:00:00' : sprintf('%02d:%02d:00', $endHour, $endMin);

                TimeSlot::firstOrCreate([
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]);
            }
        }
    }

    private function timeToMinutes($timeStr) {
        if ($timeStr === '24:00') return 24 * 60;
        $parts = explode(':', $timeStr);
        return (int)$parts[0] * 60 + (int)$parts[1];
    }

    /**
     * Resolve booking IDs from query and return current user's booking batch.
     */
    private function getUserBookingsFromQuery(Request $request): Collection
    {
        // Support both comma and hyphen as separators
        $queryString = (string) $request->query('bookings', '');
        $separator = str_contains($queryString, '-') ? '-' : ',';
        
        $ids = collect(explode($separator, $queryString))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $bookings = Booking::with(['arena', 'timeSlot', 'payment'])
            ->where('user_id', Auth::id())
            ->whereIn('id', $ids->all())
            ->get()
            ->sortBy(function (Booking $booking) {
                // Sort by start_time if available, otherwise by timeSlot->start_time
                if ($booking->start_time) {
                    return $booking->start_time;
                } elseif ($booking->timeSlot) {
                    return $booking->timeSlot->start_time;
                }
                return '00:00:00';
            })
            ->values();

        if ($bookings->count() !== $ids->count()) {
            return collect();
        }

        return $bookings;
    }

    /**
     * Get ordered standardized slots (06:00-24:00, 30 minutes each).
     */
    private function getStandardOrderedSlots(): Collection
    {
        $this->ensureStandardTimeSlots();

        $slots = [];
        for ($hour = 6; $hour < 24; $hour++) {
            for ($min = 0; $min < 60; $min += 30) {
                $startTime = sprintf('%02d:%02d:00', $hour, $min);
                $endHour = ($min + 30 >= 60) ? $hour + 1 : $hour;
                $endMin = ($min + 30 >= 60) ? 0 : $min + 30;
                $endTime = $endHour === 24 ? '00:00:00' : sprintf('%02d:%02d:00', $endHour, $endMin);
                $slots[$startTime] = $endTime;
            }
        }

        return TimeSlot::whereIn('start_time', array_keys($slots))
            ->orderBy('start_time')
            ->get()
            ->filter(function (TimeSlot $slot) use ($slots) {
                return isset($slots[$slot->start_time]) && $slot->end_time === $slots[$slot->start_time];
            })
            ->unique('start_time')
            ->values();
    }

    /**
     * Display the user's bookings.
     */
    public function myBookings()
    {
        $bookings = Booking::with(['arena', 'timeSlot'])
            ->where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('bookings.my-bookings', compact('bookings'));
    }

    /**
     * Cancel a pending booking.
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể hủy yêu cầu đang chờ xác nhận.');
        }

        $booking->update(['status' => 'cancelled']);
        
        if ($booking->payment) {
            $booking->payment->update(['status' => 'failed']);
        }

        // Gửi thông báo cho Admin và Chủ sân (nếu có)
        try {
            $admins = \App\Models\User::whereHas('role', function($q) {
                $q->where('name', 'admin');
            })->get();

            $receivers = $admins;
            if ($booking->arena->owner_id) {
                $owner = \App\Models\User::find($booking->arena->owner_id);
                if ($owner) {
                    $receivers->push($owner);
                }
            }

            $msg = Auth::user()->name . ' vừa hủy đơn đặt sân ' . $booking->arena->name;
            \Illuminate\Support\Facades\Notification::send($receivers->unique('id'), new \App\Notifications\BookingNotification(
                $booking, 
                'cancelled', 
                $msg
            ));

            // Gửi thông báo Telegram cho Admin
            if (env('TELEGRAM_BOT_TOKEN')) {
                $zaloMessage = "<b>⚠️ KHÁCH HÀNG HỦY ĐƠN ĐẶT SÂN</b>\n\n";
                $zaloMessage .= "👤 <b>Khách hàng:</b> " . Auth::user()->name . "\n";
                $zaloMessage .= "⚽ <b>Sân:</b> " . $booking->arena->name . "\n";
                $zaloMessage .= "📅 <b>Ngày:</b> " . $booking->date . "\n";
                $zaloMessage .= "⏰ <b>Giờ:</b> " . substr($booking->start_time, 0, 5) . " - " . substr($booking->end_time, 0, 5) . "\n";
                
                \App\Services\TelegramService::sendMessage($zaloMessage);
            }
        } catch (\Exception $e) {
            // Log error if needed
        }

        return back()->with('success', 'Đã hủy yêu cầu đặt sân.');
    }
}
