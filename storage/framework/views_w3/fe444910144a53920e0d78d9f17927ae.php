<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => 'My Account', 'active' => 'overview']));

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

foreach (array_filter((['title' => 'My Account', 'active' => 'overview']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $siteSetting = app(\Domain\Content\Repositories\SiteSettingRepository::class)->current();
    $user = auth()->user();
    $initials = collect(preg_split('/\s+/', trim((string) $user?->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') ?: 'U';

    // The full member menu, identical on desktop (sidebar) and mobile (drawer).
    // Network items appear as their routes are registered.
    $has = static fn (string $name): bool => \Illuminate\Support\Facades\Route::has($name);
    $nav = [
        ['key' => 'overview', 'label' => 'Overview', 'icon' => 'home', 'href' => route('account.show')],
        ['key' => 'card', 'label' => 'Membership Card', 'icon' => 'credit-card', 'href' => route('account.show').'#card'],
        ['key' => 'certificate', 'label' => 'Certificate', 'icon' => 'award', 'href' => route('account.certificate')],
    ];
    $optional = [
        ['key' => 'profile', 'label' => 'My Profile', 'icon' => 'user', 'route' => 'account.profile'],
        ['key' => 'network', 'label' => 'Network', 'icon' => 'users', 'route' => 'network.index'],
        ['key' => 'messages', 'label' => 'Messages', 'icon' => 'message-square', 'route' => 'messages.index'],
        ['key' => 'votes', 'label' => 'Votes', 'icon' => 'check-circle', 'route' => 'votes.index'],
        ['key' => 'verification', 'label' => 'Verification', 'icon' => 'fingerprint', 'route' => 'verification.index'],
        ['key' => 'plans', 'label' => 'Plans & Upgrade', 'icon' => 'gem', 'route' => 'plans.index'],
    ];
    foreach ($optional as $item) {
        if ($has($item['route'])) {
            $nav[] = ['key' => $item['key'], 'label' => $item['label'], 'icon' => $item['icon'], 'href' => route($item['route'])];
        }
    }
    $nav = array_merge($nav, [
        ['key' => 'applications', 'label' => 'Applications', 'icon' => 'file-text', 'href' => route('account.applications')],
        ['key' => 'inquiries', 'label' => 'Inquiries', 'icon' => 'message-square', 'href' => route('inquiries.track')],
        ['key' => 'documents', 'label' => 'Documents', 'icon' => 'folder-open', 'href' => route('account.documents')],
        ['key' => 'security', 'label' => 'Security', 'icon' => 'shield-check', 'href' => route('account.security')],
        ['key' => 'settings', 'label' => 'Settings', 'icon' => 'settings', 'href' => route('account.settings')],
    ]);

    // Quick links for the mobile bottom bar (the drawer holds everything).
    $tabs = collect($nav)->whereIn('key', ['overview', 'card', 'certificate', 'inquiries'])->values();
?>

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

        <?php if (isset($component)) { $__componentOriginal4232ba5ed77147a6b6573253fafb715d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4232ba5ed77147a6b6573253fafb715d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seo-head','data' => ['title' => $title,'siteSetting' => $siteSetting]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seo-head'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'site-setting' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($siteSetting)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4232ba5ed77147a6b6573253fafb715d)): ?>
<?php $attributes = $__attributesOriginal4232ba5ed77147a6b6573253fafb715d; ?>
<?php unset($__attributesOriginal4232ba5ed77147a6b6573253fafb715d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4232ba5ed77147a6b6573253fafb715d)): ?>
<?php $component = $__componentOriginal4232ba5ed77147a6b6573253fafb715d; ?>
<?php unset($__componentOriginal4232ba5ed77147a6b6573253fafb715d); ?>
<?php endif; ?>

        <link rel="manifest" href="<?php echo e(asset('manifest.webmanifest')); ?>">
        <link rel="apple-touch-icon" href="<?php echo e(asset('images/apple-touch-icon.png')); ?>">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="Underground">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <style>@view-transition { navigation: auto; }</style>

        <?php if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))): ?>
            <?php echo app('Illuminate\Foundation\Vite')->fonts(); ?>
            <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <?php endif; ?>
    </head>
    <body class="ac-body">
        <a class="ac-skip" href="#main">Skip to main content</a>

        <div class="ac-shell">
            <div class="ac-backdrop" data-ac-close hidden></div>

            <aside class="ac-side" id="ac-side" aria-label="Member menu">
                <span class="ac-grab" aria-hidden="true"></span>
                <div class="ac-side-head">
                    <a href="<?php echo e(route('account.show')); ?>" class="ac-brand" aria-label="Underground member area">
                        <span class="ac-mono" aria-hidden="true">U</span>
                        <span><b>Underground</b><small>Member Network</small></span>
                    </a>
                    <button type="button" class="ac-iconbtn ac-close" data-ac-close aria-label="Close menu"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
                </div>

                <div class="ac-sheet-user">
                    <span class="ac-avatar" aria-hidden="true"><?php echo e($initials); ?></span>
                    <span class="ac-sheet-who"><b><?php echo e($user?->name); ?></b><small><?php echo e($user?->email); ?></small></span>
                </div>

                <nav aria-label="Member area" class="ac-nav-wrap">
                    <ul class="ac-nav">
                        <?php $__currentLoopData = $nav; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a href="<?php echo e($item['href']); ?>" <?php if($active === $item['key']): ?> aria-current="page" <?php endif; ?>>
                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $item['icon']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['icon'])]); ?>
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
                                    <span><?php echo e($item['label']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="ac-signout"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'log-out']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'log-out']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><span>Sign out</span></button>
                            </form>
                        </li>
                    </ul>
                </nav>

                <div class="ac-foot">
                    <a href="<?php echo e(route('home')); ?>" class="ac-foot-link"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'globe']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'globe']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><span>Visit website</span></a>
                </div>
            </aside>

            <div class="ac-main">
                <header class="ac-mbar">
                    <a href="<?php echo e(route('account.show')); ?>" class="ac-mono ac-mono-sm" aria-label="Underground member area">U</a>
                    <span class="ac-mbar-title"><?php echo e($title); ?></span>
                    <button type="button" class="ac-avatar ac-avatar-btn" data-ac-open aria-controls="ac-side" aria-expanded="false" aria-label="Open account menu"><?php echo e($initials); ?></button>
                </header>

                <main id="main" tabindex="-1" class="ac-content">
                    <?php echo e($slot); ?>

                </main>
            </div>

            <nav class="ac-tabbar" aria-label="Quick links">
                <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($item['href']); ?>" <?php if($active === $item['key']): ?> aria-current="page" <?php endif; ?>>
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => $item['icon']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['icon'])]); ?>
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
                        <span><?php echo e($item['key'] === 'card' ? 'Card' : $item['label']); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <button type="button" data-ac-open aria-controls="ac-side" aria-label="Open full menu">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'menu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'menu']); ?>
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
                    <span>Menu</span>
                </button>
            </nav>
        </div>

        <div class="ac-toast" id="ac-toast" role="status" aria-live="polite"></div>

        <script>
            (function () {
                var side = document.getElementById('ac-side');
                var back = document.querySelector('.ac-backdrop');
                var openers = document.querySelectorAll('[data-ac-open]');
                var buzz = function () { if (navigator.vibrate) { try { navigator.vibrate(8); } catch (e) {} } };

                function setOpen(on) {
                    side.classList.toggle('is-open', on);
                    side.style.transform = '';
                    back.hidden = !on;
                    document.body.classList.toggle('ac-lock', on);
                    openers.forEach(function (b) { b.setAttribute('aria-expanded', on ? 'true' : 'false'); });
                    if (on) { var first = side.querySelector('.ac-nav a'); if (first) first.focus({ preventScroll: true }); }
                }

                openers.forEach(function (b) { b.addEventListener('click', function () { buzz(); setOpen(true); }); });
                document.querySelectorAll('[data-ac-close]').forEach(function (b) { b.addEventListener('click', function () { setOpen(false); }); });
                side.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', function () { buzz(); setOpen(false); }); });
                document.querySelectorAll('.ac-tabbar a').forEach(function (a) { a.addEventListener('click', buzz); });
                document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });

                // swipe the sheet down to dismiss (mobile)
                var startY = null, dy = 0;
                var handle = side.querySelector('.ac-grab');
                [handle, side.querySelector('.ac-side-head'), side.querySelector('.ac-sheet-user')].forEach(function (el) {
                    if (!el) return;
                    el.addEventListener('touchstart', function (e) { startY = e.touches[0].clientY; dy = 0; side.style.transition = 'none'; }, { passive: true });
                    el.addEventListener('touchmove', function (e) {
                        if (startY === null) return;
                        dy = Math.max(0, e.touches[0].clientY - startY);
                        side.style.transform = 'translateY(' + dy + 'px)';
                    }, { passive: true });
                    el.addEventListener('touchend', function () {
                        if (startY === null) return;
                        side.style.transition = '';
                        if (dy > 90) { setOpen(false); } else { side.style.transform = ''; }
                        startY = null;
                    });
                });

                if ('serviceWorker' in navigator && location.protocol === 'https:') {
                    window.addEventListener('load', function () { navigator.serviceWorker.register('/sw.js').catch(function () {}); });
                }
            })();
        </script>

        <?php echo e($scripts ?? ''); ?>

    </body>
</html>
<?php /**PATH C:\laragon\www\underground\resources\views/components/account/shell.blade.php ENDPATH**/ ?>