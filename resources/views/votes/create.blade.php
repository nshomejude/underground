<x-account.shell :title="$motion->exists ? 'Edit motion' : 'New motion'" active="votes">
    <div class="vt">
        <a href="{{ route('votes.index') }}" class="vt-back"><x-icon name="arrow-left" class="h-4 w-4" />All votes</a>
        <header class="vt-head">
            <div>
                <p class="vt-eyebrow">Member governance</p>
                <h1>{{ $motion->exists ? 'Edit motion' : 'New motion' }}</h1>
                <p class="vt-lead">Put a question to the membership. Save a draft to keep working, or publish to open it for voting.</p>
            </div>
        </header>
        @include('votes._form', [
            'motion' => $motion,
            'action' => $motion->exists ? route('votes.update', $motion) : route('votes.store'),
            'cancelUrl' => route('votes.index'),
        ])
    </div>
</x-account.shell>
