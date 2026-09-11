<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';

// Let's test 2 approaches:
// Approach 1: Remove table-layout: fixed; and put explicit widths on the <th>/<td>
// In auto layout:
// <th class="th-student-col" style="width: 240px;">
// Each day cell <th class="th-day" style="width: 20px;">
// P, A, % <th style="width: 22px;">

$html = file_get_contents('c:/Users/mukuk/Documents/GitHub/pif-meal-app/scratch/u17_rendered.html');

// Remove table-layout: fixed
$html_auto = str_replace('table-layout: fixed;', '', $html);

// In $html_auto, give th-student-col width: 220px;
$html_auto = str_replace(
    'class="th-main-header th-student-col"',
    'class="th-main-header th-student-col" style="width: 240px; min-width: 240px; max-width: 240px;"',
    $html_auto
);

// Also set width on td-student-details
$html_auto = str_replace(
    'class="td-student-details"',
    'class="td-student-details" style="width: 240px; min-width: 240px; max-width: 240px;"',
    $html_auto
);

// Make day th/td small: width: 18px
$html_auto = str_replace(
    'class="th-day"',
    'class="th-day" style="width: 18px; max-width: 22px;"',
    $html_auto
);

$dompdf = new Dompdf\Dompdf();
$dompdf->setPaper('a4', 'landscape');
$dompdf->loadHtml($html_auto);
$dompdf->render();

file_put_contents('c:/Users/mukuk/Documents/GitHub/pif-meal-app/scratch/solution.pdf', $dompdf->output());
echo "Rendered solution.pdf\n";
