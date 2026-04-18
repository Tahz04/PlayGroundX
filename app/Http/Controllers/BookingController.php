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
    public function create(Arena $arena)
    {
        $timeSlots = $this->getStandardOrderedSlots();

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
            'start_hour' => 'required|integer|min:6|max:23',
            'end_hour' => 'required|integer|min:7|max:24',
            'payment_method' => 'required|in:cash,bank_transfer',
        ], [
            'start_hour.required' => 'Vui lòng chọn giờ bắt đầu.',
            'start_hour.integer' => 'Giờ bắt đầu không hợp lệ.',
            'start_hour.min' => 'Giờ bắt đầu phải từ 06:00 trở đi.',
            'start_hour.max' => 'Giờ bắt đầu tối đa là 23:00.',
            'end_hour.required' => 'Vui lòng chọn giờ kết thúc.',
            'end_hour.integer' => 'Giờ kết thúc không hợp lệ.',
            'end_hour.min' => 'Giờ kết thúc phải từ 07:00 trở đi.',
            'end_hour.max' => 'Giờ kết thúc tối đa là 24:00.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ]);

        $startHour = (int) $request->input('start_hour');
        $endHour = (int) $request->input('end_hour');
        $paymentMethod = $request->input('payment_method');

        if ($endHour <= $startHour) {
            return back()
                ->withInput()
                ->with('error', 'Giờ kết thúc phải lớn hơn giờ bắt đầu (tối thiểu 1 tiếng).');
        }

        $orderedSlots = $this->getStandardOrderedSlots();

        $selectedSlots = $orderedSlots
            ->filter(function (TimeSlot $slot) use ($startHour, $endHour) {
                $slotStartHour = (int) substr($slot->start_time, 0, 2);

                return $slotStartHour >= $startHour && $slotStartHour < $endHour;
            })
            ->values();

        $expectedSlotCount = $endHour - $startHour;

        if ($selectedSlots->count() !== $expectedSlotCount) {
            return back()
                ->withInput()
                ->with('error', 'Khoảng giờ đã chọn chưa khả dụng hoặc bị thiếu khung giờ liên tiếp.');
        }

        $isContiguous = true;
        for ($i = 0; $i < $selectedSlots->count() - 1; $i++) {
            if ($selectedSlots[$i]->end_time !== $selectedSlots[$i + 1]->start_time) {
                $isContiguous = false;
                break;
            }
        }

        if (!$isContiguous) {
            return back()
                ->withInput()
                ->with('error', 'Hệ thống chưa có đủ khung giờ liên tiếp. Vui lòng chọn mốc thời gian khác.');
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
        $createdBookingIds = [];

        DB::transaction(function () use ($request, $slotIds, $arena, $paymentMethod, &$createdBookingIds) {
            foreach ($slotIds as $slotId) {
                $booking = Booking::create([
                    'user_id' => Auth::id(),
                    'arena_id' => $request->arena_id,
                    'date' => $request->date,
                    'time_slot_id' => $slotId,
                    'status' => 'pending',
                ]);

                $createdBookingIds[] = $booking->id;

                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $arena->price,
                    'method' => $paymentMethod,
                    'status' => $paymentMethod === 'bank_transfer' ? 'unpaid' : 'pending',
                ]);
            }
        });

        $startClock = sprintf('%02d:00', $startHour);
        $endClock = sprintf('%02d:00', $endHour);

        $bookingIdsParam = implode(',', $createdBookingIds);
        $successMessage = 'Đã tạo lịch đặt từ ' . $startClock . ' đến ' . $endClock . ' thành công.';

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
     * Ensure standard one-hour slots from 06:00 to 24:00 are available.
     */
    private function ensureStandardTimeSlots(): void
    {
        for ($hour = 6; $hour < 24; $hour++) {
            $startTime = sprintf('%02d:00:00', $hour);
            $endTime = $hour === 23 ? '00:00:00' : sprintf('%02d:00:00', $hour + 1);

            TimeSlot::firstOrCreate([
                'start_time' => $startTime,
                'end_time' => $endTime,
            ]);
        }
    }

    /**
     * Resolve booking IDs from query and return current user's booking batch.
     */
    private function getUserBookingsFromQuery(Request $request): Collection
    {
        $ids = collect(explode(',', (string) $request->query('bookings', '')))
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
     * Get ordered standardized slots (06:00-24:00, 1 hour each).
     */
    private function getStandardOrderedSlots(): Collection
    {
        $this->ensureStandardTimeSlots();

        $expectedEndByStart = collect(range(6, 23))
            ->mapWithKeys(function ($hour) {
                $startTime = sprintf('%02d:00:00', $hour);
                $endTime = $hour === 23 ? '00:00:00' : sprintf('%02d:00:00', $hour + 1);

                return [$startTime => $endTime];
            });

        return TimeSlot::whereIn('start_time', $expectedEndByStart->keys()->all())
            ->orderBy('start_time')
            ->get()
            ->filter(function (TimeSlot $slot) use ($expectedEndByStart) {
                return isset($expectedEndByStart[$slot->start_time])
                    && $slot->end_time === $expectedEndByStart[$slot->start_time];
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
}
