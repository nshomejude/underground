<x-account.shell title="Company verification" active="verification">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Verification</p>
            <h1>Company status</h1>
        </div>
    </header>
    @include('verification.partials.status', ['kind' => 'company', 'row' => $row, 'files' => $files, 'service' => $service, 'summary' => [
        'Company' => $row->company_name,
        'Registration number' => $row->registration_number,
        'Country' => $row->country,
        'Incorporated' => $row->incorporation_date?->format('j M Y'),
        'Your role' => $row->applicant_role,
    ]])
</x-account.shell>
