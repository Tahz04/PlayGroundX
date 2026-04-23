<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';
$app = app();
$app->boot();
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Auth::loginUsingId(1);
$u = Auth::user();
echo "User: " . $u->name . " Role: " . ($u->role ? $u->role->name : 'NULL') . " Role ID: " . $u->role_id . PHP_EOL;
