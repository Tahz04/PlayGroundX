<?php
require_once 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';

\$db = \$app->make('db');
\$results = \$db->select('SELECT id, status, date FROM bookings WHERE arena_id = 1 LIMIT 10');

foreach (\$results as \$row) {
    echo 'ID: ' . \$row->id . ', Status: ' . \$row->status . ', Date: ' . \$row->date . PHP_EOL;
}
