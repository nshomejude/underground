<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['name', 'label', 'value' => null, 'type' => 'text', 'rows' => null, 'hint' => null, 'max' => null, 'options' => null, 'required' => false, 'placeholder' => null, 'autocomplete' => null]));

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

foreach (array_filter((['name', 'label', 'value' => null, 'type' => 'text', 'rows' => null, 'hint' => null, 'max' => null, 'options' => null, 'required' => false, 'placeholder' => null, 'autocomplete' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $id = 'f-'.trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-');
    $has = $errors->has($key);
    $describe = trim(($has ? $id.'-err ' : '').($hint ? $id.'-hint' : ''));
    $current = old($key, $value);
?>

<div class="ac-field pf-field">
    <label for="<?php echo e($id); ?>" class="ac-label"><?php echo e($label); ?><?php if($required): ?> <span class="pf-req" aria-hidden="true">*</span><span class="sr-only">required</span><?php endif; ?></label>

    <?php if($options !== null): ?>
        <select id="<?php echo e($id); ?>" name="<?php echo e($name); ?>" class="ac-input" <?php if($describe): ?> aria-describedby="<?php echo e($describe); ?>" <?php endif; ?> <?php if($has): ?> aria-invalid="true" <?php endif; ?>>
            <option value="">Select...</option>
            <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optValue => $optLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($optValue); ?>" <?php if((string) $current === (string) $optValue): echo 'selected'; endif; ?>><?php echo e($optLabel); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    <?php elseif($rows): ?>
        <textarea id="<?php echo e($id); ?>" name="<?php echo e($name); ?>" rows="<?php echo e($rows); ?>" class="ac-input pf-ta" <?php if($max): ?> maxlength="<?php echo e($max); ?>" <?php endif; ?> <?php if($placeholder): ?> placeholder="<?php echo e($placeholder); ?>" <?php endif; ?> <?php if($describe): ?> aria-describedby="<?php echo e($describe); ?>" <?php endif; ?> <?php if($has): ?> aria-invalid="true" <?php endif; ?>><?php echo e($current); ?></textarea>
    <?php else: ?>
        <input type="<?php echo e($type); ?>" id="<?php echo e($id); ?>" name="<?php echo e($name); ?>" value="<?php echo e($current); ?>" class="ac-input" <?php if($max): ?> maxlength="<?php echo e($max); ?>" <?php endif; ?> <?php if($placeholder): ?> placeholder="<?php echo e($placeholder); ?>" <?php endif; ?> <?php if($autocomplete): ?> autocomplete="<?php echo e($autocomplete); ?>" <?php endif; ?> <?php if($required): ?> required aria-required="true" <?php endif; ?> <?php if($describe): ?> aria-describedby="<?php echo e($describe); ?>" <?php endif; ?> <?php if($has): ?> aria-invalid="true" <?php endif; ?>>
    <?php endif; ?>

    <?php if($hint): ?>
        <p id="<?php echo e($id); ?>-hint" class="pf-hint"><?php echo e($hint); ?></p>
    <?php endif; ?>
    <?php $__errorArgs = [$key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p id="<?php echo e($id); ?>-err" class="ac-err"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH C:\laragon\www\underground\resources\views/components/profile/field.blade.php ENDPATH**/ ?>