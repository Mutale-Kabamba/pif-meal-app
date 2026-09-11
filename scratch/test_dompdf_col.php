<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';

// Test 1: col with style="width: 34%"
// Test 2: col with width="34%" or width="200"
// Test 3: th with style="width: 250px" or width="250"
// Let's test in dompdf!

$html = '
<table style="width: 100%; table-layout: fixed; border: 1px solid black;">
    <colgroup>
        <col style="width: 34%;">
        <col style="width: 66%;">
    </colgroup>
    <tr>
        <td style="border: 1px solid red;">Col 1</td>
        <td style="border: 1px solid blue;">Col 2</td>
    </tr>
</table>
';

$dompdf = new Dompdf\Dompdf();
$dompdf->loadHtml($html);
$dompdf->render();

// Let's inspect
$canvas = $dompdf->getCanvas();
// Let's see what happens if we put width on the first row's TD or TH
