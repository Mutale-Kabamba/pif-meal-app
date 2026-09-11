<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';
$app = require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dompdf = new Dompdf\Dompdf();
$dompdf->setPaper('a4', 'landscape');

$html = file_get_contents('c:/Users/mukuk/Documents/GitHub/pif-meal-app/scratch/u17_rendered.html');
$dompdf->loadHtml($html);
$dompdf->render();

// Inspect the table frame
$canvas = $dompdf->getCanvas();
echo "Canvas width: " . $canvas->get_width() . ", height: " . $canvas->get_height() . "\n";

// Let's inspect the first row cells widths from Dompdf internals
$dom = $dompdf->getDom();
$xpath = new DOMXPath($dom);
$tables = $xpath->query("//table[contains(@class, 'register-table')]");
echo "Found tables: " . $tables->length . "\n";
