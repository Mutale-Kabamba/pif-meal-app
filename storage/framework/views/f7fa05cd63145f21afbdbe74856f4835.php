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
            margin: 8mm;
        }

        /* ── Page header ── */
        .page-header {
            text-align: center;
            margin-bottom: 3mm;
            padding-bottom: 2.5mm;
            border-bottom: 0.5pt solid #d1d5db;
        }
        .page-header h1 {
            font-size: 11pt;
            font-weight: bold;
            color: #111827;
        }
        .page-header .meta {
            font-size: 7pt;
            color: #6b7280;
            margin-top: 1mm;
        }

        /* ── Card grid (table-based for DomPDF) ── */
        .cards-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .cards-table td {
            width: 50%;
            padding: 1.5mm;
            vertical-align: top;
            height: 58mm;
        }

        /* ── Individual card ── */
        .card {
            width: 100%;
            height: 55mm;
            border: 1pt solid #374151;
            border-radius: 0;
            padding: 3mm 3.5mm 2.5mm 3.5mm;
            text-align: center;
        }
        .card-label {
            font-size: 5.5pt;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 1mm;
        }
        .card-name {
            font-size: 10pt;
            font-weight: bold;
            color: #111827;
            margin-bottom: 1mm;
            overflow: hidden;
        }
        .card-divider {
            border: none;
            border-top: 0.3pt solid #e5e7eb;
            margin: 1mm 0;
        }
        .card-code {
            font-size: 13pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #059669;
            letter-spacing: 3px;
            margin-bottom: 1mm;
        }
        .card-qr img {
            width: 18mm;
            height: 18mm;
            display: block;
            margin: 0 auto;
        }

        /* ── Page footer ── */
        .page-footer {
            text-align: center;
            font-size: 6.5pt;
            color: #9ca3af;
            margin-top: 2.5mm;
            padding-top: 2mm;
            border-top: 0.3pt solid #e5e7eb;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>
<?php $chunks = $beneficiaries->chunk(8); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $chunks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageIndex => $pageCards): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="<?php echo e($pageIndex > 0 ? 'page-break' : ''); ?>">

        <div class="page-header">
            <h1><?php echo e($project->name); ?></h1>
            <div class="meta">
                Budget Code: <?php echo e($project->budget_code); ?> &nbsp;&middot;&nbsp;
                Generated: <?php echo e(now()->format('F j, Y')); ?> &nbsp;&middot;&nbsp;
                Page <?php echo e($pageIndex + 1); ?>/<?php echo e($chunks->count()); ?> &nbsp;&middot;&nbsp;
                <?php echo e($beneficiaries->count()); ?> total cards
            </div>
        </div>

        <table class="cards-table">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pageCards->chunk(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $beneficiary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $qrSvg = base64_encode(
                                QrCode::size(90)->margin(1)->generate($beneficiary->qr_token)
                            );
                        ?>
                        <td>
                            <div class="card">
                                <div class="card-label">Nutrition Program</div>
                                <div class="card-name"><?php echo e($beneficiary->name); ?></div>
                                <hr class="card-divider">
                                <div class="card-code"><?php echo e($beneficiary->shortcode); ?></div>
                                <div class="card-qr">
                                    <img src="data:image/svg+xml;base64,<?php echo e($qrSvg); ?>" alt="QR">
                                </div>
                            </div>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->count() === 1): ?>
                        <td></td>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </table>

        <div class="page-footer">
            Nutrition Monitoring System &nbsp;&middot;&nbsp; <?php echo e($project->name); ?> &nbsp;&middot;&nbsp; Printed: <?php echo e(now()->format('d M Y H:i')); ?>

        </div>

    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</body>
</html>
<?php /**PATH C:\Users\mukuk\Documents\GitHub\pif-meal-app\resources\views/pdf/card-sheet.blade.php ENDPATH**/ ?>