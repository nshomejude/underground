<x-account.shell title="Identity verification" active="verification">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Verification</p>
            <h1>Identity status</h1>
        </div>
    </header>
    @include('verification.partials.status', ['kind' => 'identity', 'row' => $row, 'files' => $files, 'service' => $service, 'summary' => [
        'Document' => config('network.identity_document_types.'.$row->document_type),
        'Issuing country' => $row->document_country,
        'Name on document' => $row->full_name_on_document,
        'Expiry' => $row->document_expiry?->format('j M Y'),
        'Number (last characters)' => $row->document_number_last4,
    ]])
</x-account.shell>
