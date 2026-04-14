<?php

namespace App\Http\Controllers;

use App\Models\Arena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerArenaController extends Controller
{
    /**
     * Display a listing of the owner's arenas.
     */
    public function index()
    {
        $user = Auth::user();
        $arenas = $user->arenas()->latest()->get();
        
        return view('owner.arenas.index', compact('arenas'));
    }

    /**
     * Show the form for creating a new arena.
     */
    public function create()
    {
        return view('owner.arenas.create');
    }

    /**
     * Store a newly created arena in storage.
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
        ]);

        Auth::user()->arenas()->create($validated);

        return redirect()->route('owner.arenas.index')->with('success', 'Thêm sân thành công!');
    }

    /**
     * Show the form for editing the specified arena.
     */
    public function edit(Arena $arena)
    {
        $this->authorizeOwner($arena);
        return view('owner.arenas.edit', compact('arena'));
    }

    /**
     * Update the specified arena in storage.
     */
    public function update(Request $request, Arena $arena)
    {
        $this->authorizeOwner($arena);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Sân 5,Sân 7,Sân 11',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $arena->update($validated);

        return redirect()->route('owner.arenas.index')->with('success', 'Cập nhật sân thành công!');
    }

    /**
     * Remove the specified arena from storage.
     */
    public function destroy(Arena $arena)
    {
        $this->authorizeOwner($arena);
        $arena->delete();

        return redirect()->route('owner.arenas.index')->with('success', 'Xóa sân thành công!');
    }

    /**
     * Verify that the authenticated user owns the arena.
     */
    private function authorizeOwner(Arena $arena)
    {
        if ($arena->owner_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền quản lý sân này.');
        }
    }
}
