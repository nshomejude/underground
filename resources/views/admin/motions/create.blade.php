<x-admin.shell title="New Motion" max-width="max-w-4xl">
    <div class="vt">
        @include('votes._form', [
            'motion' => $motion,
            'action' => route('admin.motions.store'),
            'cancelUrl' => route('admin.motions.index'),
        ])
    </div>
</x-admin.shell>
