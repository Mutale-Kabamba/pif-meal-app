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

function printFrames($frame, $depth = 0) {
    $class = get_class($frame);
    $node = $frame->get_node()->nodeName;
    if ($node === 'table') {
        echo str_repeat(' ', $depth) . "$node ($class)\n";
        if (method_exists($frame, 'get_cellmap')) {
            $cols = $frame->get_cellmap()->get_columns();
            echo "Cols count: " . count($cols) . "\n";
            foreach ($cols as $i => $c) {
                echo "  Col $i: width=" . $c["width"] . "\n";
            }
        }
    }
    foreach ($frame->get_children() as $c) {
        printFrames($c, $depth + 1);
    }
}

printFrames($dompdf->getTree()->get_root());
