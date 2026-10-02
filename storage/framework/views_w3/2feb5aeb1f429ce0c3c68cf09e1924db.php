<?php $__env->startSection('content'); ?>
    
    <?php $__currentLoopData = $intro; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <p style="margin:<?php echo e($loop->first ? '26px' : '16px'); ?> 0 0 0;font-family:Helvetica,Arial,sans-serif;font-size:17px;line-height:28px;color:#D2CCC0;text-align:center;"><?php echo e($line); ?></p>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <?php if(! empty($summary)): ?>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:30px;background-color:#1B1A1F;border:1px solid #3A3326;">
            <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="padding:16px 22px;<?php echo e(! $loop->last ? 'border-bottom:1px solid #2F2B22;' : ''); ?>">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td class="stack" valign="middle" style="font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:18px;letter-spacing:2px;text-transform:uppercase;color:#C9B27E;"><?php echo e($row[0]); ?></td>
                                <td class="stack val" valign="middle" align="right" style="font-family:<?php echo e(! empty($row[2]) ? "'Courier New',Courier,monospace" : 'Helvetica,Arial,sans-serif'); ?>;font-size:16px;line-height:24px;font-weight:bold;color:#F3EFE6;text-align:right;"><?php echo e($row[1]); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    <?php endif; ?>

    
    <?php if(! empty($actionUrl)): ?>
        <div style="padding-top:34px;">
            <?php echo $__env->make('emails.partials.button', ['url' => $actionUrl, 'label' => $actionText], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <p style="margin:22px 0 0 0;font-family:Helvetica,Arial,sans-serif;font-size:13px;line-height:21px;color:#A39E94;text-align:center;">
            If the button does not work, paste this link into your browser:<br>
            <a href="<?php echo e($actionUrl); ?>" style="color:#D9D3C7;word-break:break-all;"><?php echo e($actionUrl); ?></a>
        </p>
    <?php endif; ?>

    
    <?php if(! empty($steps)): ?>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:34px;">
            <tr>
                <td style="padding-bottom:6px;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:16px;font-weight:bold;letter-spacing:3px;text-transform:uppercase;color:#C9A25A;">Where to begin</td>
            </tr>
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="padding:16px 0;<?php echo e(! $loop->last ? 'border-bottom:1px solid #2F2B22;' : ''); ?>">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="48" valign="top" style="width:48px;font-family:Georgia,serif;font-size:26px;line-height:28px;color:#C9A25A;">0<?php echo e($loop->iteration); ?></td>
                                <td valign="top" style="font-family:Helvetica,Arial,sans-serif;font-size:16px;line-height:25px;color:#D2CCC0;"><strong style="color:#F3EFE6;"><?php echo e($step[0]); ?>.</strong> <?php echo e($step[1]); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    <?php endif; ?>

    
    <?php $__currentLoopData = $outro; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <p style="margin:<?php echo e($loop->first ? '28px' : '14px'); ?> 0 0 0;font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:25px;color:#B9B4AC;text-align:center;"><?php echo e($line); ?></p>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:34px;">
        <tr>
            <td align="center" style="font-family:Helvetica,Arial,sans-serif;font-size:15px;line-height:24px;color:#B9B4AC;">
                With discretion,<br>
                <?php if(! empty($signedBy)): ?>
                    <span style="display:inline-block;padding-top:8px;font-family:Georgia,serif;font-size:24px;line-height:30px;font-style:italic;color:#F3EFE6;"><?php echo e($signedBy); ?></span><br>
                    <span style="font-size:13px;line-height:20px;letter-spacing:2px;text-transform:uppercase;color:#A39E94;"><?php echo e($signedTitle ?? ''); ?></span>
                <?php else: ?>
                    <span style="display:inline-block;padding-top:6px;font-family:Georgia,serif;font-size:19px;line-height:26px;color:#F3EFE6;">The <?php echo e($appName); ?> team</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('emails.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\underground\resources\views/emails/notification.blade.php ENDPATH**/ ?>