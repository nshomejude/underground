<?php
    $tabs = [
        ['label' => 'Home', 'icon' => 'home', 'href' => url('/')],
        ['label' => 'Capabilities', 'icon' => 'landmark', 'href' => route('capabilities.index')],
        ['label' => 'Reach', 'icon' => 'globe', 'href' => route('global-reach')],
        ['label' => 'Insights', 'icon' => 'newspaper', 'href' => route('insights.index')],
        ['label' => 'Contact', 'icon' => 'mail', 'href' => route('inquiries.create')],
    ];

    $currentUrl = url()->current();
?>

<nav
    class="fixed inset-x-0 bottom-0 z-40 flex items-stretch border-t border-border bg-surface pb-[env(safe-area-inset-bottom)] lg:hidden"
    aria-label="Primary"
>
    <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php ($isActive = ! str_contains($tab['href'], '#') && rtrim($tab['href'], '/') === rtrim($currentUrl, '/')); ?>

        <a
            href="<?php echo e($tab['href']); ?>"
            class="flex min-w-0 flex-1 flex-col items-center justify-center gap-1 py-2.5 whitespace-nowrap text-[10px] font-semibold uppercase tracking-wide transition-colors <?php echo e($isActive ? 'text-gold' : 'text-muted hover:text-gold'); ?>"
        >
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $tab['icon'],'class' => 'h-5 w-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tab['icon']),'class' => 'h-5 w-5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
            <?php echo e($tab['label']); ?>

        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
<?php /**PATH C:\laragon\www\underground\resources\views/components/mobile-tab-bar.blade.php ENDPATH**/ ?>