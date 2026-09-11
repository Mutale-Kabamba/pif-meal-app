<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';

function testHtml($name, $html) {
    $dompdf = new Dompdf\Dompdf();
    $dompdf->setPaper('a4', 'landscape');
    $dompdf->loadHtml($html);
    $dompdf->render();
    file_put_contents("scratch/test_$name.pdf", $dompdf->output());
}

// Case 1: Colgroup with width attributes vs style
$html1 = '
<style>
@page { size: A4 landscape; margin: 10mm; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; }
td, th { border: 1px solid black; }
</style>
<table>
    <colgroup>
        <col style="width: 250px;">
        <col style="width: 20px;">
        <col style="width: 20px;">
    </colgroup>
    <tr>
        <th>Student Details</th>
        <th>Day 1</th>
        <th>Day 2</th>
    </tr>
</table>
';
testHtml('1', $html1);

// Case 2: col with width="250"
$html2 = '
<style>
@page { size: A4 landscape; margin: 10mm; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; }
td, th { border: 1px solid black; }
</style>
<table>
    <colgroup>
        <col width="250">
        <col width="20">
        <col width="20">
    </colgroup>
    <tr>
        <th>Student Details</th>
        <th>Day 1</th>
        <th>Day 2</th>
    </tr>
</table>
';
testHtml('2', $html2);

// Case 3: table-layout: auto (NOT fixed)
$html3 = '
<style>
@page { size: A4 landscape; margin: 10mm; }
table { width: 100%; border-collapse: collapse; }
td, th { border: 1px solid black; }
</style>
<table>
    <tr>
        <th style="width: 250px;">Student Details</th>
        <th>Day 1</th>
        <th>Day 2</th>
    </tr>
</table>
';
testHtml('3', $html3);

// Case 4: First row has colspan vs no colspan!
$html4 = '
<style>
@page { size: A4 landscape; margin: 10mm; }
table { width: 100%; border-collapse: collapse; table-layout: fixed; }
td, th { border: 1px solid black; }
</style>
<table>
    <tr>
        <th style="width: 250px;" rowspan="2">Student Details</th>
        <th colspan="2">Month</th>
    </tr>
    <tr>
        <th>D1</th>
        <th>D2</th>
    </tr>
</table>
';
testHtml('4', $html4);
