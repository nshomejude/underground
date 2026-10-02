@php($count = \App\Services\MessageService::unreadCount())
@if ($count > 0)
    <span {{ $attributes->merge(['class' => 'msg-badge']) }} aria-label="{{ $count }} unread {{ $count === 1 ? 'message' : 'messages' }}">{{ $count > 99 ? '99+' : $count }}</span>
@endif
