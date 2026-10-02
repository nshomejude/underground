<?php if (isset($component)) { $__componentOriginala4880b879d71a876b7c4b3137ea6c55a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala4880b879d71a876b7c4b3137ea6c55a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.message-unread-badge','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('message-unread-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala4880b879d71a876b7c4b3137ea6c55a)): ?>
<?php $attributes = $__attributesOriginala4880b879d71a876b7c4b3137ea6c55a; ?>
<?php unset($__attributesOriginala4880b879d71a876b7c4b3137ea6c55a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala4880b879d71a876b7c4b3137ea6c55a)): ?>
<?php $component = $__componentOriginala4880b879d71a876b7c4b3137ea6c55a; ?>
<?php unset($__componentOriginala4880b879d71a876b7c4b3137ea6c55a); ?>
<?php endif; ?><?php /**PATH C:\Users\PC\AppData\Local\Temp/lar56EA.blade.php ENDPATH**/ ?>