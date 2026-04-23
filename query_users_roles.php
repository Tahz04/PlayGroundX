<?php
require_once "bootstrap/app.php";
$app = app();

echo "=== First User with Role ===" . PHP_EOL;
$user = App\Models\User::with("role")->first();
if ($user) {
    echo json_encode($user->toArray(), JSON_PRETTY_PRINT) . PHP_EOL;
}

echo PHP_EOL . "=== All Users ===" . PHP_EOL;
$users = App\Models\User::all();
echo json_encode($users->toArray(), JSON_PRETTY_PRINT) . PHP_EOL;

echo PHP_EOL . "=== All Roles ===" . PHP_EOL;
$roles = App\Models\Role::all();
echo json_encode($roles->toArray(), JSON_PRETTY_PRINT) . PHP_EOL;
