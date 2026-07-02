<?php
// Script pour vérifier les coordonnées du bac B004 en local
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bins = DB::select('SELECT code, latitude, longitude, location FROM bins ORDER BY code');
foreach ($bins as $b) {
    echo "Code: {$b->code} | lat: {$b->latitude} | lng: {$b->longitude} | location: {$b->location}\n";
}
