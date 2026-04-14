<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arena;

class MapController extends Controller
{
    public function index()
    {
        $arenas = Arena::whereNotNull('latitude')->whereNotNull('longitude')->get();
        return view('map', compact('arenas'));
    }

    public function getArenas()
    {
        $arenas = Arena::whereNotNull('latitude')->whereNotNull('longitude')->get();
        return response()->json($arenas);
    }
}
