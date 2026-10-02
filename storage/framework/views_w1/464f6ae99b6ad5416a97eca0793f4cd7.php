<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'eyebrow' => null,
    'align' => 'left',
    'tag' => 'h2',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'eyebrow' => null,
    'align' => 'left',
    'tag' => 'h2',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $alignClasses = $align === 'center' ? 'items-center text-center' : 'items-start text-left';
?>

<div <?php echo e($attributes->merge(['class' => "flex flex-col gap-3 {$alignClasses}"])); ?>>
    <?php if($eyebrow): ?>
        <span class="inline-flex items-center gap-3 text-xs font-semibold uppercase tracking-[0.2em] text-gold">
            <?php if (! ($align === 'center')): ?>
                <span class="h-px w-8 bg-gold"></span>
            <?php endif; ?>
            <?php echo e($eyebrow); ?>

        </span>
    <?php endif; ?>

    <<?php echo e($tag); ?> class="font-serif text-3xl font-semibold leading-tight text-cream sm:text-4xl lg:text-5xl">
        <?php echo e($slot); ?>

    </<?php echo e($tag); ?>>
</div>
<?php /**PATH C:\laragon\www\underground\resources\views/components/section-heading.blade.php ENDPATH**/ ?>