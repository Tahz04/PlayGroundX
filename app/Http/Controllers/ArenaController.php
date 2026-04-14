<?php

namespace App\Http\Controllers;

use App\Models\Arena;
use Illuminate\Http\Request;

class ArenaController extends Controller
{
    /**
     * Display a listing of the resource for public.
     */
    public function publicIndex(Request $request)
    {
        $query = Arena::where('status', true);

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
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Sân 5,Sân 7,Sân 11',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Arena::create($request->all());

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
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Sân 5,Sân 7,Sân 11',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $arena->update($request->all());

        return redirect()->route('admin.arenas.index')->with('success', 'Đã cập nhật thông tin sân thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Arena $arena)
    {
        $arena->delete();
        return redirect()->route('admin.arenas.index')->with('success', 'Đã xóa sân thành công!');
    }
}
