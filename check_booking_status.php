<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->boot();

$b = \App\Models\Booking::whereIn('arena_id', [1])->select('id', 'status', 'date')->get();
foreach ($b as $x) {
    echo 'ID: ' . $x->id . ' Status: ' . $x->status . ' Date: ' . $x->date . PHP_EOL;
}
