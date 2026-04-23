<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';
\ = app();
\->boot();
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Auth::loginUsingId(1);
\ = Auth::user();
echo "User: " . \->name . " Role: " . (\->role ? \->role->name : 'NULL') . " Role ID: " . \->role_id . "\n";
