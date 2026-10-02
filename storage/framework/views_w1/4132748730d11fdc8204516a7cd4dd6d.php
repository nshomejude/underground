<?php
    $sectorMap = (array) config('network.sectors');
    $roleMap = (array) config('network.supply_chain_roles');
    $kindMap = (array) config('network.seeking_kinds');
    $visMap = (array) config('network.profile_visibility');
    $failed = old('section');
    $list = fn (string $section, string $key, ?array $current) => $failed === $section ? (array) old($key, []) : ($current ?? []);
    $missing = collect($checklist)->where('done', false)->values();
    $sections = [
        'identity' => 'Identity', 'about' => 'About', 'location' => 'Location', 'focus' => 'Sectors & role',
        'seeking' => 'Seeking / offering', 'services' => 'Services', 'portfolio' => 'Portfolio',
        'organisation' => 'Organisation', 'links' => 'Links', 'visibility' => 'Visibility',
    ];
    $rowsFor = function (string $section, ?array $saved) use ($failed) {
        $rows = $failed === $section ? array_values((array) old($section, [])) : array_values($saved ?? []);

        return array_pad($rows, min(\App\Services\ProfileService::MAX_ROWS, max(3, count($rows) + 2)), []);
    };
    $languages = $failed === 'location' ? (is_array(old('languages')) ? implode(', ', old('languages')) : old('languages')) : implode(', ', $profile->languages ?? []);
?>

<?php if (isset($component)) { $__componentOriginal3b7a85420992c282ff4e01c409b9a4b5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3b7a85420992c282ff4e01c409b9a4b5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.account.shell','data' => ['title' => 'My Profile','active' => 'profile']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('account.shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'My Profile','active' => 'profile']); ?>
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Member Network</p>
            <h1>My Profile</h1>
            <p class="ac-sub">A complete profile is how other members find, match with and trust you.</p>
        </div>
        <a href="#preview" class="ac-btn"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'eye']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'eye']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>Preview as others see me</a>
    </header>

    <?php if(session('status')): ?>
        <p class="ac-flash ac-flash-ok" role="status"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check-circle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><?php echo e(session('status')); ?></p>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <p class="ac-flash ac-flash-warn" role="alert">Some details need attention. Review the highlighted fields below.</p>
    <?php endif; ?>

    <?php if(! $canUseNetwork): ?>
        <div class="ac-flash ac-flash-warn" role="note">
            <p>You can build your profile now. To appear in the member directory you also need an approved membership and a verified email, and then identity verification.</p>
        </div>
    <?php elseif(! $identityVerified): ?>
        <div class="ac-flash ac-flash-warn" role="note">
            <p>Your profile is not listed in the directory yet: listing also requires identity verification.</p>
            <?php if(\Illuminate\Support\Facades\Route::has('verification.index')): ?>
                <a href="<?php echo e(route('verification.index')); ?>" class="ac-btn">Verify identity</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    
    <details class="pf-bar" id="checklist-m">
        <summary>
            <span class="pf-bar-top"><b><?php echo e($score); ?>% complete</b><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-down']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></span>
            <span class="pf-track" aria-hidden="true"><i style="width: <?php echo e($score); ?>%"></i></span>
        </summary>
        <?php echo $__env->make('account.profile._checklist', ['missing' => $missing, 'score' => $score], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </details>

    <nav class="pf-nav" aria-label="Profile sections">
        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="#<?php echo e($key); ?>"><?php echo e($label); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <a href="#preview">Preview</a>
    </nav>

    <div class="pf-layout">
        <div class="pf-main">

            
            <section class="ac-panel pf-sec" id="identity" aria-labelledby="h-identity">
                <p class="ac-eyebrow">1 of 10</p>
                <h2 id="h-identity">Identity &amp; headline</h2>

                <div class="pf-avatar-row">
                    <?php if($profile->avatarUrl()): ?>
                        <img class="pc-avatar pf-avatar" src="<?php echo e($profile->avatarUrl()); ?>" alt="Your current profile photo" width="96" height="96">
                    <?php else: ?>
                        <span class="pc-avatar pc-initials pf-avatar" aria-hidden="true"><?php echo e(app(\App\Services\ProfileService::class)->initials($profile->display_name ?: $profile->user->name)); ?></span>
                    <?php endif; ?>
                    <div class="pf-avatar-actions">
                        <form method="POST" action="<?php echo e(route('account.profile.avatar')); ?>" enctype="multipart/form-data" class="ac-stack">
                            <?php echo csrf_field(); ?>
                            <div class="ac-field">
                                <label for="avatar" class="ac-label">Profile photo</label>
                                <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" class="ac-input pf-file" aria-describedby="avatar-hint <?php $__errorArgs = ['avatar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> avatar-err <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" <?php $__errorArgs = ['avatar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> aria-invalid="true" <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>>
                                <p id="avatar-hint" class="pf-hint">JPG, PNG or WebP, up to 3 MB. Cropped to a square.</p>
                                <?php $__errorArgs = ['avatar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p id="avatar-err" class="ac-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <button type="submit" class="ac-btn ac-btn-solid">Upload photo</button>
                        </form>
                        <?php if($profile->avatar_path): ?>
                            <form method="POST" action="<?php echo e(route('account.profile.avatar.destroy')); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="ac-btn">Remove photo</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <form method="POST" action="<?php echo e(route('account.profile.update')); ?>" class="ac-stack" novalidate>
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="section" value="identity">
                    <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'display_name','label' => 'Display name','value' => $profile->display_name,'max' => '80','required' => true,'autocomplete' => 'name','hint' => 'Shown to other members instead of your account name.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'display_name','label' => 'Display name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->display_name),'max' => '80','required' => true,'autocomplete' => 'name','hint' => 'Shown to other members instead of your account name.']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'headline','label' => 'Headline','value' => $profile->headline,'max' => '160','placeholder' => 'e.g. Infrastructure financier connecting capital to African energy projects','hint' => 'One line, up to 160 characters.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'headline','label' => 'Headline','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->headline),'max' => '160','placeholder' => 'e.g. Infrastructure financier connecting capital to African energy projects','hint' => 'One line, up to 160 characters.']); ?>
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
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save identity</button>
                </form>
            </section>

            
            <section class="ac-panel pf-sec" id="about" aria-labelledby="h-about">
                <p class="ac-eyebrow">2 of 10</p>
                <h2 id="h-about">About</h2>
                <form method="POST" action="<?php echo e(route('account.profile.update')); ?>" class="ac-stack" novalidate>
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="section" value="about">
                    <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'bio','label' => 'Bio','value' => $profile->bio,'rows' => '7','max' => '2000','hint' => 'At least 120 characters counts toward completeness. Say who you are, what you have built and how you work. Up to 2000 characters.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'bio','label' => 'Bio','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->bio),'rows' => '7','max' => '2000','hint' => 'At least 120 characters counts toward completeness. Say who you are, what you have built and how you work. Up to 2000 characters.']); ?>
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
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save bio</button>
                </form>
            </section>

            
            <section class="ac-panel pf-sec" id="location" aria-labelledby="h-location">
                <p class="ac-eyebrow">3 of 10</p>
                <h2 id="h-location">Location &amp; languages</h2>
                <form method="POST" action="<?php echo e(route('account.profile.update')); ?>" class="ac-stack" novalidate>
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="section" value="location">
                    <div class="pf-two">
                        <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'city','label' => 'City','value' => $profile->city,'max' => '100','autocomplete' => 'address-level2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'city','label' => 'City','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->city),'max' => '100','autocomplete' => 'address-level2']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'country','label' => 'Country','value' => $profile->country,'max' => '100','autocomplete' => 'country-name']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'country','label' => 'Country','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->country),'max' => '100','autocomplete' => 'country-name']); ?>
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
                    <div class="ac-field pf-field">
                        <label for="languages" class="ac-label">Languages</label>
                        <input type="text" id="languages" name="languages" value="<?php echo e($languages); ?>" class="ac-input" placeholder="English, French, Arabic" aria-describedby="languages-hint <?php $__errorArgs = ['languages'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> languages-err <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" <?php $__errorArgs = ['languages'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> aria-invalid="true" <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>>
                        <p id="languages-hint" class="pf-hint">Separate with commas. Up to 10.</p>
                        <?php $__errorArgs = ['languages'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p id="languages-err" class="ac-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php $__errorArgs = ['languages.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ac-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save location</button>
                </form>
            </section>

            
            <section class="ac-panel pf-sec" id="focus" aria-labelledby="h-focus">
                <p class="ac-eyebrow">4 of 10</p>
                <h2 id="h-focus">Sectors &amp; supply-chain role</h2>
                <form method="POST" action="<?php echo e(route('account.profile.update')); ?>" class="ac-stack" novalidate>
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="section" value="focus">
                    <?php $selSectors = $list('focus', 'sectors', $profile->sectors); $selRoles = $list('focus', 'supply_chain_roles', $profile->supply_chain_roles); ?>
                    <fieldset class="pf-set" aria-describedby="sectors-hint <?php $__errorArgs = ['sectors'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> sectors-err <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <legend class="ac-label">Sectors you work in</legend>
                        <p id="sectors-hint" class="pf-hint">Pick all that apply. At least one is needed for matching.</p>
                        <div class="pf-chips">
                            <?php $__currentLoopData = $sectorMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="pf-chip"><input type="checkbox" name="sectors[]" value="<?php echo e($value); ?>" <?php if(in_array($value, $selSectors, true)): echo 'checked'; endif; ?>><span><?php echo e($label); ?></span></label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['sectors'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p id="sectors-err" class="ac-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php $__errorArgs = ['sectors.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ac-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </fieldset>
                    <fieldset class="pf-set" aria-describedby="roles-hint <?php $__errorArgs = ['supply_chain_roles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> roles-err <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <legend class="ac-label">Where you sit in the supply chain</legend>
                        <p id="roles-hint" class="pf-hint">Used to suggest complementary partners.</p>
                        <div class="pf-chips">
                            <?php $__currentLoopData = $roleMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="pf-chip"><input type="checkbox" name="supply_chain_roles[]" value="<?php echo e($value); ?>" <?php if(in_array($value, $selRoles, true)): echo 'checked'; endif; ?>><span><?php echo e($label); ?></span></label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['supply_chain_roles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p id="roles-err" class="ac-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php $__errorArgs = ['supply_chain_roles.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ac-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </fieldset>
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save sectors &amp; role</button>
                </form>
            </section>

            
            <section class="ac-panel pf-sec" id="seeking" aria-labelledby="h-seeking">
                <p class="ac-eyebrow">5 of 10</p>
                <h2 id="h-seeking">Seeking &amp; offering</h2>
                <form method="POST" action="<?php echo e(route('account.profile.update')); ?>" class="ac-stack" novalidate>
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="section" value="seeking">
                    <?php $selKinds = $list('seeking', 'seeking_kinds', $profile->seeking_kinds); ?>
                    <fieldset class="pf-set" aria-describedby="kinds-hint <?php $__errorArgs = ['seeking_kinds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> kinds-err <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <legend class="ac-label">I am looking for</legend>
                        <p id="kinds-hint" class="pf-hint">Pick what you want from the network.</p>
                        <div class="pf-chips">
                            <?php $__currentLoopData = $kindMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="pf-chip"><input type="checkbox" name="seeking_kinds[]" value="<?php echo e($value); ?>" <?php if(in_array($value, $selKinds, true)): echo 'checked'; endif; ?>><span><?php echo e($label); ?></span></label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['seeking_kinds'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p id="kinds-err" class="ac-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php $__errorArgs = ['seeking_kinds.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ac-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </fieldset>
                    <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'seeking_summary','label' => 'What I am looking for','value' => $profile->seeking_summary,'rows' => '4','max' => '1000']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'seeking_summary','label' => 'What I am looking for','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->seeking_summary),'rows' => '4','max' => '1000']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'offering_summary','label' => 'What I offer','value' => $profile->offering_summary,'rows' => '4','max' => '1000']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'offering_summary','label' => 'What I offer','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->offering_summary),'rows' => '4','max' => '1000']); ?>
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
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save seeking &amp; offering</button>
                </form>
            </section>

            
            <section class="ac-panel pf-sec" id="services" aria-labelledby="h-services">
                <p class="ac-eyebrow">6 of 10</p>
                <h2 id="h-services">Services</h2>
                <p class="ac-lead">What you can deliver for other members. List between 1 and <?php echo e(\App\Services\ProfileService::MAX_ROWS); ?>.</p>
                <form method="POST" action="<?php echo e(route('account.profile.update')); ?>" class="ac-stack" novalidate data-rows="services">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="section" value="services">
                    <?php $__errorArgs = ['services'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ac-err" role="alert"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div class="pf-rows" data-rows-list data-max="<?php echo e(\App\Services\ProfileService::MAX_ROWS); ?>">
                        <?php $__currentLoopData = $rowsFor('services', $profile->services); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('account.profile._row', ['kind' => 'services', 'i' => $i, 'row' => $row], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <template data-row-template><?php echo $__env->make('account.profile._row', ['kind' => 'services', 'i' => '__i__', 'row' => []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></template>
                    <button type="button" class="ac-btn pf-add" data-add hidden><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>Add another service</button>
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save services</button>
                </form>
            </section>

            
            <section class="ac-panel pf-sec" id="portfolio" aria-labelledby="h-portfolio">
                <p class="ac-eyebrow">7 of 10</p>
                <h2 id="h-portfolio">Portfolio</h2>
                <p class="ac-lead">Notable work that builds trust. Up to <?php echo e(\App\Services\ProfileService::MAX_ROWS); ?> items.</p>
                <form method="POST" action="<?php echo e(route('account.profile.update')); ?>" class="ac-stack" novalidate data-rows="portfolio">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="section" value="portfolio">
                    <?php $__errorArgs = ['portfolio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="ac-err" role="alert"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div class="pf-rows" data-rows-list data-max="<?php echo e(\App\Services\ProfileService::MAX_ROWS); ?>">
                        <?php $__currentLoopData = $rowsFor('portfolio', $profile->portfolio); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo $__env->make('account.profile._row', ['kind' => 'portfolio', 'i' => $i, 'row' => $row], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <template data-row-template><?php echo $__env->make('account.profile._row', ['kind' => 'portfolio', 'i' => '__i__', 'row' => []], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></template>
                    <button type="button" class="ac-btn pf-add" data-add hidden><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>Add another item</button>
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save portfolio</button>
                </form>
            </section>

            
            <section class="ac-panel pf-sec" id="organisation" aria-labelledby="h-organisation">
                <p class="ac-eyebrow">8 of 10</p>
                <h2 id="h-organisation">Organisation</h2>
                <form method="POST" action="<?php echo e(route('account.profile.update')); ?>" class="ac-stack" novalidate>
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="section" value="organisation">
                    <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'organisation_name','label' => 'Organisation name','value' => $profile->organisation_name,'max' => '160','autocomplete' => 'organization']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'organisation_name','label' => 'Organisation name','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->organisation_name),'max' => '160','autocomplete' => 'organization']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'organisation_role','label' => 'Your role','value' => $profile->organisation_role,'max' => '120','autocomplete' => 'organization-title']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'organisation_role','label' => 'Your role','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->organisation_role),'max' => '120','autocomplete' => 'organization-title']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'organisation_size','label' => 'Organisation size','value' => $profile->organisation_size,'max' => '60','placeholder' => 'e.g. 11-50 people']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'organisation_size','label' => 'Organisation size','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->organisation_size),'max' => '60','placeholder' => 'e.g. 11-50 people']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'organisation_website','label' => 'Organisation website','type' => 'url','value' => $profile->organisation_website,'placeholder' => 'https://']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'organisation_website','label' => 'Organisation website','type' => 'url','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->organisation_website),'placeholder' => 'https://']); ?>
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
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save organisation</button>
                </form>
            </section>

            
            <section class="ac-panel pf-sec" id="links" aria-labelledby="h-links">
                <p class="ac-eyebrow">9 of 10</p>
                <h2 id="h-links">Links</h2>
                <form method="POST" action="<?php echo e(route('account.profile.update')); ?>" class="ac-stack" novalidate>
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="section" value="links">
                    <?php if (isset($component)) { $__componentOriginald25c57b977efe3b905602f74f9bfd1c0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald25c57b977efe3b905602f74f9bfd1c0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'website','label' => 'Website','type' => 'url','value' => $profile->website,'placeholder' => 'https://']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'website','label' => 'Website','type' => 'url','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->website),'placeholder' => 'https://']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'linkedin_url','label' => 'LinkedIn','type' => 'url','value' => $profile->linkedin_url,'placeholder' => 'https://www.linkedin.com/in/...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'linkedin_url','label' => 'LinkedIn','type' => 'url','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->linkedin_url),'placeholder' => 'https://www.linkedin.com/in/...']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile.field','data' => ['name' => 'public_email','label' => 'Public contact email','type' => 'email','value' => $profile->public_email,'hint' => 'Optional. Shown to other members. Your sign-in email is never shown.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile.field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'public_email','label' => 'Public contact email','type' => 'email','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile->public_email),'hint' => 'Optional. Shown to other members. Your sign-in email is never shown.']); ?>
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
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save links</button>
                </form>
            </section>

            
            <section class="ac-panel pf-sec" id="visibility" aria-labelledby="h-visibility">
                <p class="ac-eyebrow">10 of 10</p>
                <h2 id="h-visibility">Visibility</h2>
                <p class="ac-lead">Appearing in the member directory requires an approved membership and a verified email, plus completed identity verification. Choosing "visible" only makes you eligible; it does not bypass verification. Hidden profiles never appear.</p>
                <form method="POST" action="<?php echo e(route('account.profile.update')); ?>" class="ac-stack" novalidate>
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="section" value="visibility">
                    <fieldset class="pf-set" <?php $__errorArgs = ['visibility'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> aria-describedby="vis-err" <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>>
                        <legend class="ac-label">Who can see my profile</legend>
                        <?php $__currentLoopData = $visMap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="pf-radio"><input type="radio" name="visibility" value="<?php echo e($value); ?>" <?php if(old('visibility', $profile->visibility) === $value): echo 'checked'; endif; ?>><span><?php echo e($label); ?></span></label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__errorArgs = ['visibility'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p id="vis-err" class="ac-err"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </fieldset>
                    <label class="pf-radio pf-toggle">
                        <input type="hidden" name="open_to_collaboration" value="0">
                        <input type="checkbox" name="open_to_collaboration" value="1" <?php if((bool) (old('section') === 'visibility' ? old('open_to_collaboration') : $profile->open_to_collaboration)): echo 'checked'; endif; ?>>
                        <span>I am open to collaboration requests</span>
                    </label>
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save visibility</button>
                </form>
            </section>

            
            <section class="ac-panel pf-sec" id="preview" aria-labelledby="h-preview">
                <p class="ac-eyebrow">Preview</p>
                <h2 id="h-preview">How others see me</h2>
                <p class="ac-lead">This is the card other members see in the directory. Reload after saving to refresh it.</p>
                <?php if (isset($component)) { $__componentOriginal2299f79f212ad7b2f1b6f23328beba2f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2299f79f212ad7b2f1b6f23328beba2f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.profile-card','data' => ['profile' => $profile]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('profile-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['profile' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profile)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2299f79f212ad7b2f1b6f23328beba2f)): ?>
<?php $attributes = $__attributesOriginal2299f79f212ad7b2f1b6f23328beba2f; ?>
<?php unset($__attributesOriginal2299f79f212ad7b2f1b6f23328beba2f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2299f79f212ad7b2f1b6f23328beba2f)): ?>
<?php $component = $__componentOriginal2299f79f212ad7b2f1b6f23328beba2f; ?>
<?php unset($__componentOriginal2299f79f212ad7b2f1b6f23328beba2f); ?>
<?php endif; ?>
            </section>
        </div>

        <aside class="pf-aside" aria-label="Profile completeness">
            <div class="ac-panel pf-sticky" id="checklist">
                <div class="ac-ringwrap">
                    <div class="ac-ring" role="img" aria-label="<?php echo e($score); ?> percent complete">
                        <svg width="120" height="120" viewBox="0 0 120 120" aria-hidden="true">
                            <circle class="tr" cx="60" cy="60" r="50" pathLength="100"/>
                            <circle class="pr" cx="60" cy="60" r="50" pathLength="100" style="--off: <?php echo e(100 - $score); ?>"/>
                        </svg>
                        <b><?php echo e($score); ?>%</b>
                    </div>
                    <p><?php echo e($score >= 80 ? 'Your profile is complete enough to be matched.' : 'Reach 80% to be treated as a complete profile.'); ?></p>
                </div>
                <?php echo $__env->make('account.profile._checklist', ['missing' => $missing, 'score' => $score], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </aside>
    </div>

    <script>
        (function () {
            document.querySelectorAll('[data-rows]').forEach(function (form) {
                var list = form.querySelector('[data-rows-list]');
                var tpl = form.querySelector('[data-row-template]');
                var add = form.querySelector('[data-add]');
                var max = parseInt(list.getAttribute('data-max'), 10);
                var next = list.children.length;
                var visible = function () { return list.querySelectorAll('[data-row]:not([hidden])').length; };
                var renumber = function () {
                    var n = 0;
                    list.querySelectorAll('[data-row]:not([hidden])').forEach(function (r) { n++; r.querySelector('[data-row-n]').textContent = n; });
                    add.disabled = visible() >= max;
                };
                add.hidden = false;
                list.querySelectorAll('[data-remove]').forEach(function (b) { b.hidden = false; });
                add.addEventListener('click', function () {
                    if (visible() >= max) { return; }
                    var holder = document.createElement('div');
                    holder.innerHTML = tpl.innerHTML.replace(/__i__/g, next++);
                    var row = holder.firstElementChild;
                    row.querySelector('[data-remove]').hidden = false;
                    list.appendChild(row);
                    renumber();
                    var first = row.querySelector('input, textarea, select');
                    if (first) { first.focus(); }
                });
                list.addEventListener('click', function (e) {
                    var btn = e.target.closest('[data-remove]');
                    if (!btn) { return; }
                    var row = btn.closest('[data-row]');
                    row.querySelectorAll('input, textarea, select').forEach(function (f) { f.value = ''; });
                    row.hidden = true;
                    renumber();
                    add.focus();
                });
                renumber();
            });
        })();
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3b7a85420992c282ff4e01c409b9a4b5)): ?>
<?php $attributes = $__attributesOriginal3b7a85420992c282ff4e01c409b9a4b5; ?>
<?php unset($__attributesOriginal3b7a85420992c282ff4e01c409b9a4b5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3b7a85420992c282ff4e01c409b9a4b5)): ?>
<?php $component = $__componentOriginal3b7a85420992c282ff4e01c409b9a4b5; ?>
<?php unset($__componentOriginal3b7a85420992c282ff4e01c409b9a4b5); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\underground\resources\views/account/profile/edit.blade.php ENDPATH**/ ?>