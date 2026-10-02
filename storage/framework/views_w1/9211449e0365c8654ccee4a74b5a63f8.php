<?php
    $siteSetting = app(\Domain\Content\Repositories\SiteSettingRepository::class)->current();

    $navGroups = [
        'Company' => [
            'About' => route('about'),
            'Team' => route('team'),
            'Careers' => route('careers'),
            'Partners' => route('partners'),
            'Contact' => route('contact'),
        ],
        'What We Do' => [
            'Capabilities' => route('capabilities.index'),
            'Expertise' => route('sectors.index'),
            'Global Reach' => route('global-reach'),
            'Portfolio' => route('portfolio'),
            'Projects' => route('projects'),
        ],
        'Resources' => [
            'Insights' => route('insights.index'),
            'Events' => route('events'),
            'Terms' => route('terms'),
            'Privacy' => route('privacy'),
        ],
    ];
?>

<footer class="border-t border-border bg-ink pb-[calc(5rem+env(safe-area-inset-bottom))] lg:pb-0">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-4 lg:gap-12">
            <div class="col-span-2 flex flex-col items-start gap-4 lg:col-span-1">
                <?php if (isset($component)) { $__componentOriginala04d4f76939ca6fbfd6d251d0478d748 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala04d4f76939ca6fbfd6d251d0478d748 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seal','data' => ['size' => 84]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 84]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala04d4f76939ca6fbfd6d251d0478d748)): ?>
<?php $attributes = $__attributesOriginala04d4f76939ca6fbfd6d251d0478d748; ?>
<?php unset($__attributesOriginala04d4f76939ca6fbfd6d251d0478d748); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala04d4f76939ca6fbfd6d251d0478d748)): ?>
<?php $component = $__componentOriginala04d4f76939ca6fbfd6d251d0478d748; ?>
<?php unset($__componentOriginala04d4f76939ca6fbfd6d251d0478d748); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal282477a66be4db050d8fe3eab27ef4c6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal282477a66be4db050d8fe3eab27ef4c6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.brand-mark','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('brand-mark'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal282477a66be4db050d8fe3eab27ef4c6)): ?>
<?php $attributes = $__attributesOriginal282477a66be4db050d8fe3eab27ef4c6; ?>
<?php unset($__attributesOriginal282477a66be4db050d8fe3eab27ef4c6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal282477a66be4db050d8fe3eab27ef4c6)): ?>
<?php $component = $__componentOriginal282477a66be4db050d8fe3eab27ef4c6; ?>
<?php unset($__componentOriginal282477a66be4db050d8fe3eab27ef4c6); ?>
<?php endif; ?>
                <p class="max-w-xs text-sm leading-relaxed text-body">
                    A global network delivering discreet, high-conviction execution across sectors and borders.
                </p>
                <ul class="flex flex-col gap-1.5 text-sm text-body">
                    <li><a href="mailto:info@un-der.com" class="transition-colors hover:text-gold">info@un-der.com</a></li>
                    <li><a href="tel:+15715089170" class="transition-colors hover:text-gold">+1-571-508-9170</a></li>
                    <li class="text-muted">Washington, DC &middot; Douala &middot; Abidjan &middot; Lagos &middot; Paris</li>
                </ul>
            </div>

            <?php $__currentLoopData = $navGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $links): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <nav aria-label="<?php echo e($group); ?>">
                    <h2 class="text-[10px] font-semibold uppercase tracking-[0.25em] text-muted"><?php echo e($group); ?></h2>
                    <ul class="mt-4 space-y-2.5">
                        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $href): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a href="<?php echo e($href); ?>" class="text-sm text-body transition-colors hover:text-gold"><?php echo e($label); ?></a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </nav>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if(! empty($siteSetting->socialLinks)): ?>
            <nav aria-label="Social" class="mt-10 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 border-t border-border pt-8 sm:justify-start">
                <?php $__currentLoopData = $siteSetting->socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($social['url']); ?>" class="text-xs font-semibold uppercase tracking-widest text-body transition-colors hover:text-gold" rel="noopener" target="_blank">
                        <?php echo e($social['label']); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </nav>
        <?php endif; ?>

        <div class="mt-12 flex flex-col items-center gap-2 border-t border-border pt-6 text-center sm:flex-row sm:justify-between sm:text-left">
            <p class="text-[11px] uppercase tracking-widest text-muted">
                &copy; <?php echo e(now()->year); ?> <?php echo e($siteSetting->siteName); ?> Inc. All rights reserved.
            </p>
            <div class="flex flex-col items-center gap-1 sm:items-end">
                <?php if($siteSetting->footerNote): ?>
                    <p class="text-[11px] uppercase tracking-widest text-muted"><?php echo e($siteSetting->footerNote); ?></p>
                <?php endif; ?>
                <p class="text-[11px] uppercase tracking-widest text-muted">
                    Powered by <a href="https://opesware.com" class="text-body transition-colors hover:text-gold" rel="noopener" target="_blank">opesware.com</a>
                </p>
            </div>
        </div>
    </div>
</footer>
<?php /**PATH C:\laragon\www\underground\resources\views/components/site-footer.blade.php ENDPATH**/ ?>