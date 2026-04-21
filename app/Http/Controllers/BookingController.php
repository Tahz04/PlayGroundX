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
        $bookedSlotIds = Booking::where('arena_id', $arena->id)
            ->where('date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->pluck('time_slot_id')
            ->toArray();

        return view('bookings.create', compact('arena', 'timeSlots', 'bookedSlotIds', 'date'));
    }

    /**
     * AJAX endpoint to get booked slots for a date.
     */
    public function getBookedSlots(Request $request, Arena $arena)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        $bookedSlotIds = Booking::where('arena_id', $arena->id)
            ->where('date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->pluck('time_slot_id')
            ->toArray();

        return response()->json([
            'bookedSlotIds' => $bookedSlotIds
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
        $bookedSlotIds = Booking::where('arena_id', $request->arena_id)
            ->where('date', $request->date)
            ->whereIn('time_slot_id', $slotIds)
            ->where('status', '!=', 'cancelled')
            ->pluck('time_slot_id')
            ->all();

        if (!empty($bookedSlotIds)) {
            $bookedTimes = TimeSlot::whereIn('id', $bookedSlotIds)
                ->orderBy('start_time')
                ->get()
                ->map(fn (TimeSlot $slot) => $slot->formattedTime())
                ->implode(', ');

            return back()
                ->withInput()
                ->with('error', 'Các khung giờ sau đã có người đặt: ' . $bookedTimes . '. Vui lòng chọn giờ khác.');
        }

        $arena = Arena::findOrFail((int) $request->arena_id);
        
        // Calculate total price based on duration (Rate per Hour)
        $totalPrice = ($durationMinutes / 60) * $arena->price;

        // Apply promotion: 10% discount for bookings >= 3 hours (180 mins)
        if ($durationMinutes >= 180) {
            $totalPrice = $totalPrice * 0.9;
        }
        
        $createdBookingIds = [];

        DB::transaction(function () use ($request, $selectedSlots, $arena, $paymentMethod, $totalPrice, &$createdBookingIds) {
            $slotCount = $selectedSlots->count();
            $amountPerSlot = floor($totalPrice / $slotCount);
            $remainder = $totalPrice - ($amountPerSlot * $slotCount);

            foreach ($selectedSlots as $index => $slot) {
                $booking = Booking::create([
                    'user_id' => Auth::id(),
                    'arena_id' => $request->arena_id,
                    'date' => $request->date,
                    'time_slot_id' => $slot->id,
                    'status' => 'pending',
                ]);

                $createdBookingIds[] = $booking->id;

                // Add remainder to the last slot's payment to ensure total matches exactly
                $currentAmount = ($index === $slotCount - 1) ? ($amountPerSlot + $remainder) : $amountPerSlot;

                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $currentAmount,
                    'method' => $paymentMethod,
                    'status' => $paymentMethod === 'bank_transfer' ? 'unpaid' : 'pending',
                ]);
            }
        });

        $bookingIdsParam = implode('-', $createdBookingIds);
        $successMessage = 'Đã tạo lịch đặt từ ' . $startTimeStr . ' đến ' . $endTimeStr . ' thành công.';

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

        $startClock = date('H:i', strtotime($firstBooking->timeSlot->start_time));
        $endClock = $lastBooking->timeSlot->end_time === '00:00:00'
            ? '24:00'
            : date('H:i', strtotime($lastBooking->timeSlot->end_time));

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
            ->sortBy(fn (Booking $booking) => $booking->timeSlot->start_time)
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

        return back()->with('success', 'Đã hủy yêu cầu đặt sân.');
    }
}
