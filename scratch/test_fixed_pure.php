<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';

// In DomPDF's Table_Layout_Fixed class:
// How does Table_Layout_Fixed distribute widths?
// Let's inspect Dompdf\FrameReflower\Table::reflower

$html = '
<style>
@page { size: A4 landscape; margin: 10mm; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; }
td, th { border: 1px solid black; }
</style>
<table>
    <tr>
        <th style="width: 250px;">Student Details</th>';
for ($i = 1; $i <= 22; $i++) {
    $html .= '<th style="width: 20px;">D' . $i . '</th>';
}
$html .= '<th style="width: 25px;">P</th><th style="width: 25px;">A</th><th style="width: 30px;">%</th>
    </tr>
    <tr>
        <td>1. Carol Siabalengu (Team: U17 Girls)</td>';
for ($i = 1; $i <= 22; $i++) {
    $html .= '<td>✓</td>';
}
$html .= '<td>20</td><td>2</td><td>91%</td>
    </tr>
</table>
';

$dompdf = new Dompdf\Dompdf();
$dompdf->setPaper('a4', 'landscape');
$dompdf->loadHtml($html);
$dompdf->render();
file_put_contents("scratch/test_fixed_pure.pdf", $dompdf->output());
