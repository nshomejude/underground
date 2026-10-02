

<aside class="msg-list" aria-label="Conversations">
    <header class="msg-list-head">
        <h1>Messages</h1>
        <p>Private, between connected members.</p>
    </header>

    <?php if($conversations->isEmpty()): ?>
        <div class="msg-empty-list">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'message-square']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'message-square']); ?>
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
            <p>No conversations yet.</p>
        </div>
    <?php else: ?>
        <ul class="msg-items">
            <?php $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $name = \App\Services\ConversationService::nameOf($c->other);
                    $last = $c->lastMessage;
                ?>
                <li>
                    <a href="<?php echo e(route('messages.show', $c)); ?>" class="msg-item <?php if($activeId === $c->id): ?> is-active <?php endif; ?>" <?php if($activeId === $c->id): ?> aria-current="page" <?php endif; ?>>
                        <span class="msg-avatar" aria-hidden="true"><?php echo e(\App\Services\ConversationService::initials($name)); ?></span>
                        <span class="msg-item-body">
                            <span class="msg-item-top">
                                <b class="msg-item-name"><?php echo e($name); ?></b>
                                <?php if($last): ?>
                                    <time class="msg-item-time" datetime="<?php echo e($last->created_at->toIso8601String()); ?>" data-msg-list-time><?php echo e($last->created_at->isToday() ? $last->created_at->format('H:i') : $last->created_at->format('j M')); ?></time>
                                <?php endif; ?>
                            </span>
                            <span class="msg-item-bottom">
                                <span class="msg-item-preview <?php if($c->unread): ?> is-unread <?php endif; ?>">
                                    <?php if($last): ?>
                                        <?php if($last->sender_id === auth()->id()): ?><i>You:</i> <?php endif; ?><?php echo e(\Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $last->body), 70)); ?>

                                    <?php else: ?>
                                        Say hello. No messages yet.
                                    <?php endif; ?>
                                </span>
                                <?php if($c->unread): ?>
                                    <span class="msg-badge" aria-label="<?php echo e($c->unread); ?> unread"><?php echo e($c->unread > 99 ? '99+' : $c->unread); ?></span>
                                <?php endif; ?>
                            </span>
                        </span>
                    </a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
</aside>
<?php /**PATH C:\laragon\www\underground\resources\views/messages/_list.blade.php ENDPATH**/ ?>