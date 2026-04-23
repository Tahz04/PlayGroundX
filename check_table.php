<?php
require 'bootstrap/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel');

$table_info = DB::select('PRAGMA table_info(bookings)');
echo "Bookings Table Columns:\n";
print_r($table_info);
?>
