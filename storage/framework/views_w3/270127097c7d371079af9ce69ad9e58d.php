<?php ($count = \App\Services\MessageService::unreadCount()); ?>
<?php if($count > 0): ?>
    <span <?php echo e($attributes->merge(['class' => 'msg-badge'])); ?> aria-label="<?php echo e($count); ?> unread <?php echo e($count === 1 ? 'message' : 'messages'); ?>"><?php echo e($count > 99 ? '99+' : $count); ?></span>
<?php endif; ?>
<?php /**PATH C:\laragon\www\underground\resources\views/components/message-unread-badge.blade.php ENDPATH**/ ?>