<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Beneficiary Cards - <?php echo e($project->name); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; }

        @page {
            size: 210mm 297mm;
            margin: 6mm;
        }

        /* Outer card grid */
        /* border-collapse merges adjacent borders - single line between cards */
        .cards-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .cards-table td.card-cell {
            width: 50%;
            height: 57mm;
            border: 0.75pt solid #374151;
            padding: 0;
            vertical-align: middle;
        }

        /* Inner card layout (text | QR) */
        .card-inner {
            width: 100%;
            height: 57mm;
            border-collapse: collapse;
        }
        .text-cell {
            vertical-align: middle;
            text-align: left;
            padding: 3mm 2mm 3mm 4mm;
        }
        .qr-cell {
            width: 38mm;
            vertical-align: middle;
            text-align: center;
            padding: 2mm 2.5mm;
        }

        /* Card text content */
        .card-sublabel {
            font-size: 6pt;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1mm;
        }
        .card-title {
            font-size: 18pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2mm;
        }
        .card-name {
            font-size: 12pt;
            font-weight: bold;
            color: #374151;
            margin-bottom: 0.5mm;
        }
        .card-code {
            font-size: 15pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #059669;
            letter-spacing: 2px;
            margin-bottom: 1.5mm;
        }
        .card-project {
            font-size: 8pt;
            color: #6b7280;
        }

        /* QR image */
        .qr-cell img {
            width: 30mm;
            height: 30mm;
            display: block;
            margin: 0 auto;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>
<?php $chunks = $beneficiaries->chunk(10); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $chunks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageIndex => $pageCards): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="<?php echo e($pageIndex > 0 ? 'page-break' : ''); ?>">
        <table class="cards-table">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pageCards->chunk(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $beneficiary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $qrSvg = base64_encode(
                                QrCode::size(90)->margin(1)->generate($beneficiary->qr_token)
                            );
                        ?>
                        <td class="card-cell">
                            <table class="card-inner">
                                <tr>
                                    <td class="text-cell">
                                        <div class="card-sublabel">Nutrition Program</div>
                                        <div class="card-title">Meal Card</div>
                                        <div class="card-name"><?php echo e($beneficiary->name); ?></div>
                                        <div class="card-code"><?php echo e($beneficiary->shortcode); ?></div>
                                        <div class="card-project"><?php echo e($project->name); ?></div>
                                    </td>
                                    <td class="qr-cell">
                                        <img src="data:image/svg+xml;base64,<?php echo e($qrSvg); ?>" alt="QR">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->count() === 1): ?>
                        <td class="card-cell"></td>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </table>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html>
<?php /**PATH C:\Users\mukuk\Documents\GitHub\pif-meal-app\resources\views/pdf/card-sheet.blade.php ENDPATH**/ ?>