<?php
require_once 'bootstrap/app.php';
$app = app();
$results = Illuminate\Support\Facades\DB::select('SELECT id, start_time, end_time, time_slot_id FROM bookings LIMIT 5');
foreach($results as $row) {
    echo 'ID: ' . $row->id . ', Start: ' . $row->start_time . ', End: ' . $row->end_time . ', Slot: ' . $row->time_slot_id . PHP_EOL;
}
