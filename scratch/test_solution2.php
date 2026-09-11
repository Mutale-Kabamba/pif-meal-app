<?php

require_once 'c:/Users/mukuk/Documents/GitHub/pif-meal-app/vendor/autoload.php';

$html = file_get_contents('c:/Users/mukuk/Documents/GitHub/pif-meal-app/scratch/u17_rendered.html');

// 1. Remove table-layout: fixed; and remove <colgroup>
$html = str_replace('table-layout: fixed;', '', $html);
$html = preg_replace('/<colgroup>.*?<\/colgroup>/s', '', $html);

// 2. Set width on th-student-col and td-student-details
$html = str_replace(
    '.th-student-col {',
    '.th-student-col { width: 220px; min-width: 200px; ',
    $html
);
$html = str_replace(
    '.td-student-details {',
    '.td-student-details { width: 220px; min-width: 200px; ',
    $html
);

// 3. Set compact width on day columns
$html = str_replace(
    '.th-day {',
    '.th-day { width: 18px; max-width: 24px; ',
    $html
);

// 4. Set compact width on summary columns
$html = str_replace(
    '.th-summary {',
    '.th-summary { width: 24px; max-width: 30px; ',
    $html
);
$html = str_replace(
    '.td-p {',
    '.td-p { width: 24px; max-width: 30px; ',
    $html
);
$html = str_replace(
    '.td-a {',
    '.td-a { width: 24px; max-width: 30px; ',
    $html
);
$html = str_replace(
    '.td-rate {',
    '.td-rate { width: 28px; max-width: 35px; ',
    $html
);

$dompdf = new Dompdf\Dompdf();
$dompdf->setPaper('a4', 'landscape');
$dompdf->loadHtml($html);
$dompdf->render();

file_put_contents('c:/Users/mukuk/Documents/GitHub/pif-meal-app/scratch/solution2.pdf', $dompdf->output());
echo "Rendered solution2.pdf\n";
