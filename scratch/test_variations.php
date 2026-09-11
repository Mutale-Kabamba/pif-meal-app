<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';

$html = file_get_contents('c:/Users/mukuk/Documents/GitHub/pif-meal-app/scratch/u17_rendered.html');

// Let's test what in u17_rendered causes it:
// Is it table-layout: fixed?
// Is it <colgroup>?
// Is it rowspan="3"?
// Is it the number of columns?

function testVariations($name, $modHtml) {
    $dompdf = new Dompdf\Dompdf();
    $dompdf->setPaper('a4', 'landscape');
    $dompdf->loadHtml($modHtml);
    $dompdf->render();
    file_put_contents("scratch/var_$name.pdf", $dompdf->output());
}

// Variation A: Remove <colgroup>
$htmlA = preg_replace('/<colgroup>.*?<\/colgroup>/s', '', $html);
testVariations('A_no_colgroup', $htmlA);

// Variation B: Remove table-layout: fixed
$htmlB = str_replace('table-layout: fixed;', '', $html);
testVariations('B_no_fixed', $htmlB);

// Variation C: Change <col style="width: 34%;"> to <col width="220"> and others to <col width="20">
$htmlC = preg_replace('/<colgroup>.*?<\/colgroup>/s', '<colgroup><col width="220">' . str_repeat('<col width="22">', 22) . '<col width="25"><col width="25"><col width="30"></colgroup>', $html);
testVariations('C_col_fixed_pts', $htmlC);

// Variation D: Put width: 220px on the actual th/td of student column!
$htmlD = str_replace('class="th-main-header th-student-col"', 'class="th-main-header th-student-col" style="width: 220px; min-width: 220px;"', $html);
testVariations('D_th_width', $htmlD);
