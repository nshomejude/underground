<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['compact' => false]));

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

foreach (array_filter((['compact' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<span <?php echo e($attributes->merge(['class' => 'inline-flex items-center gap-3'])); ?>>
    <span class="flex h-9 w-9 shrink-0 items-center justify-center border border-gold font-serif text-lg font-bold text-gold">
        U
    </span>
    <span class="flex flex-col leading-none">
        <span class="font-serif text-base font-semibold tracking-wide text-cream">UNDERGROUND</span>
        <?php if (! ($compact)): ?>
            <span class="mt-1 hidden text-[9px] min-[340px]:block font-semibold uppercase tracking-[0.3em] text-muted">
                &mdash; Power Beneath The Surface
            </span>
        <?php endif; ?>
    </span>
</span>
<?php /**PATH C:\laragon\www\underground\resources\views/components/brand-mark.blade.php ENDPATH**/ ?>