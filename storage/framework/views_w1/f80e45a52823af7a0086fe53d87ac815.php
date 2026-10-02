<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'size' => 96,
    'variant' => 'gold',
    'alt' => 'Underground Network seal',
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
    'size' => 96,
    'variant' => 'gold',
    'alt' => 'Underground Network seal',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $variant = in_array($variant, ['gold', 'platinum', 'ink'], true) ? $variant : 'gold';
?>


<img
    src="<?php echo e(asset('images/seal/seal-'.$variant.'.png')); ?>"
    srcset="<?php echo e(asset('images/seal/seal-'.$variant.'.png')); ?> 1x, <?php echo e(asset('images/seal/seal-'.$variant.'@2x.png')); ?> 2x"
    width="<?php echo e($size); ?>"
    height="<?php echo e($size); ?>"
    alt="<?php echo e($alt); ?>"
    decoding="async"
    <?php echo e($attributes->merge(['class' => 'inline-block shrink-0'])); ?>

    style="width:<?php echo e($size); ?>px;height:<?php echo e($size); ?>px"
>
<?php /**PATH C:\laragon\www\underground\resources\views/components/seal.blade.php ENDPATH**/ ?>