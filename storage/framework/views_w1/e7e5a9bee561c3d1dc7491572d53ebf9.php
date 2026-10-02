<?php
    $sectorOptions = (array) config('network.sectors');
    $n = is_numeric($i) ? $i + 1 : '#';
?>
<fieldset class="pf-row" data-row>
    <legend class="pf-row-title"><?php echo e($kind === 'services' ? 'Service' : 'Portfolio item'); ?> <span data-row-n><?php echo e($n); ?></span></legend>
    <button type="button" class="pf-remove" data-remove hidden aria-label="Remove this <?php echo e($kind === 'services' ? 'service' : 'portfolio item'); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'x']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></button>
    <?php if($kind === 'services'): ?>
        <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'services['.$i.'][title]','label' => 'Title','value' => $row['title'] ?? null,'max' => '120']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('services['.$i.'][title]'),'label' => 'Title','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['title'] ?? null),'max' => '120']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $attributes = $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $component = $__componentOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'services['.$i.'][description]','label' => 'Description','value' => $row['description'] ?? null,'rows' => '3','max' => '600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('services['.$i.'][description]'),'label' => 'Description','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['description'] ?? null),'rows' => '3','max' => '600']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $attributes = $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $component = $__componentOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'services['.$i.'][sector]','label' => 'Sector','value' => $row['sector'] ?? null,'options' => $sectorOptions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('services['.$i.'][sector]'),'label' => 'Sector','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['sector'] ?? null),'options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sectorOptions)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $attributes = $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $component = $__componentOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'portfolio['.$i.'][title]','label' => 'Title','value' => $row['title'] ?? null,'max' => '120']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('portfolio['.$i.'][title]'),'label' => 'Title','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['title'] ?? null),'max' => '120']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $attributes = $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $component = $__componentOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'portfolio['.$i.'][summary]','label' => 'Summary','value' => $row['summary'] ?? null,'rows' => '3','max' => '600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('portfolio['.$i.'][summary]'),'label' => 'Summary','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['summary'] ?? null),'rows' => '3','max' => '600']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $attributes = $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $component = $__componentOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
        <div class="pf-two">
            <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'portfolio['.$i.'][year]','label' => 'Year','type' => 'number','value' => $row['year'] ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('portfolio['.$i.'][year]'),'label' => 'Year','type' => 'number','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['year'] ?? null)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $attributes = $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $component = $__componentOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'portfolio['.$i.'][client_type]','label' => 'Client type or sector','value' => $row['client_type'] ?? null,'max' => '120']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('portfolio['.$i.'][client_type]'),'label' => 'Client type or sector','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['client_type'] ?? null),'max' => '120']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $attributes = $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $component = $__componentOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
        </div>
        <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'portfolio['.$i.'][link]','label' => 'Link (optional)','type' => 'url','value' => $row['link'] ?? null,'placeholder' => 'https://']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('portfolio['.$i.'][link]'),'label' => 'Link (optional)','type' => 'url','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['link'] ?? null),'placeholder' => 'https://']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $attributes = $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__attributesOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0)): ?>
<?php $component = $__componentOriginald25c57b977efe3b905602f74f9bfd1c0; ?>
<?php unset($__componentOriginald25c57b977efe3b905602f74f9bfd1c0); ?>
<?php endif; ?>
    <?php endif; ?>
</fieldset>
<?php /**PATH C:\laragon\www\underground\resources\views/account/profile/_row.blade.php ENDPATH**/ ?>