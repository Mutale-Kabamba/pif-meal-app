<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';
$app = require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$b = App\Models\Beneficiary::where('name', 'like', '%Siabal%')->first();
if ($b) {
    echo "Found: {$b->name}, team_id: {$b->team_id}, team: " . ($b->team ? $b->team->name : 'null') . "\n";
    $project = $b->team ? $b->team->project : null;
    echo "Project: " . ($project ? $project->name : 'null') . "\n";
} else {
    echo "Not found\n";
}
