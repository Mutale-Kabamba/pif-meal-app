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

// Let's find the Table_Frame_Decorator
function findTableDecorator($frame) {
    if ($frame instanceof Dompdf\FrameDecorator\Table) {
        return $frame;
    }
    foreach ($frame->get_children() as $child) {
        $res = findTableDecorator($child);
        if ($res) return $res;
    }
    return null;
}

$root = $dompdf->getTree()->get_root();
$table = findTableDecorator($root);
if ($table) {
    $cellmap = $table->get_cellmap();
    $cols = $cellmap->get_columns();
    echo "Total columns in cellmap: " . count($cols) . "\n";
    foreach ($cols as $i => $c) {
        echo "Col $i: x=" . $c["x"] . ", width=" . $c["width"] . "\n";
    }
} else {
    echo "Table not found\n";
}
