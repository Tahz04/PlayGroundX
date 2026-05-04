<?php

namespace App\Http\Controllers;

use App\Models\Arena;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArenaController extends Controller
{
    /**
     * Display available time slots for all arenas on a chosen date.
     */
    public function availableIndex(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $query = Arena::whereIn('status', ['active', 'maintenance']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%");
            });
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $arenas = $query->orderBy('name')->paginate(20)->withQueryString();

        $bookingsByArena = Booking::whereIn('arena_id', $arenas->pluck('id'))
            ->where('date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->with('timeSlot')
            ->get()
            ->groupBy('arena_id')
            ->map(function ($bookings) {
                return $bookings->map(function ($b) {
                    if ($b->start_time && $b->end_time) {
                        $end = substr($b->end_time, 0, 5);
                        return [
                            'start' => substr($b->start_time, 0, 5),
                            'end'   => $end === '00:00' ? '24:00' : $end,
                        ];
                    } elseif ($b->timeSlot) {
                        return [
                            'start' => substr($b->timeSlot->start_time, 0, 5),
                            'end'   => $b->timeSlot->end_time === '00:00:00' ? '24:00' : substr($b->timeSlot->end_time, 0, 5),
                        ];
                    }
                    return null;
                })->filter()->values()->toArray();
            });

        return view('arenas.available', compact('arenas', 'bookingsByArena', 'date'));
    }

    /**
     * Display a listing of the resource for public.
     */
    public function publicIndex(Request $request)
    {
        $query = Arena::whereIn('status', ['active', 'maintenance']);

        // Search by name or location
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('location', 'like', "%$search%");
            });
        }

        // Filter by type
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        $arenas = $query->orderBy('created_at', 'desc')->paginate(12);
        
        return view('arenas.index', compact('arenas'));
    }

    /**
     * Display the specified resource for public.
     */
    public function show(Arena $arena)
    {
        // Only allow viewing active or maintenance arenas
        if (!in_array($arena->status, ['active', 'maintenance'])) {
            abort(404);
        }
        return view('arenas.show', compact('arena'));
    }

    /**
     * Display a listing of the resource for admin.
     */
    public function index()
    {
        $arenas = Arena::orderBy('created_at', 'desc')->get();
        return view('admin.arenas.index', compact('arenas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.arenas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Sân 5,Sân 7,Sân 11',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Xử lý upload ảnh
        foreach (['image', 'image_1', 'image_2'] as $imgField) {
            if ($request->hasFile($imgField)) {
                $file = $request->file($imgField);
                $filename = time() . '_' . $imgField . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $file->getClientOriginalName());
                $validated[$imgField] = $file->storeAs('arenas', $filename, 'public');
            }
        }

        Arena::create($validated);

        return redirect()->route('admin.arenas.index')->with('success', 'Đã thêm sân mới thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Arena $arena)
    {
        return view('admin.arenas.edit', compact('arena'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Arena $arena)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'type'      => 'required|string|in:Sân 5,Sân 7,Sân 11',
            'location'  => 'required|string|max:255',
            'price'     => 'required|numeric|min:0',
            'status'    => 'required|string|in:active,maintenance,inactive',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_1'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_2'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Xử lý upload ảnh mới
        foreach (['image', 'image_1', 'image_2'] as $imgField) {
            if ($request->hasFile($imgField)) {
                // Xóa ảnh cũ nếu có
                if ($arena->$imgField && Storage::disk('public')->exists($arena->$imgField)) {
                    Storage::disk('public')->delete($arena->$imgField);
                }
                
                $file = $request->file($imgField);
                $filename = time() . '_' . $imgField . '_' . preg_replace('/[^a-zA-Z0-9]/', '_', $file->getClientOriginalName());
                $validated[$imgField] = $file->storeAs('arenas', $filename, 'public');
            }
        }

        $arena->update($validated);

        return redirect()->route('admin.arenas.index')->with('success', 'Đã cập nhật thông tin sân thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Arena $arena)
    {
        // Xóa ảnh khi xóa sân
        foreach (['image', 'image_1', 'image_2'] as $imgField) {
            if ($arena->$imgField && Storage::disk('public')->exists($arena->$imgField)) {
                Storage::disk('public')->delete($arena->$imgField);
            }
        }
        
        $arena->delete();
        return redirect()->route('admin.arenas.index')->with('success', 'Đã xóa sân thành công!');
    }
}