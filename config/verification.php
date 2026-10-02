<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Identity and company verification (owned by the Verification workstream)
|--------------------------------------------------------------------------
| Storage disk/path and validity months live in config/network.php
| (verification_disk, verification_path, verification_valid_months).
*/

return [

    // Plain-language review window shown to members.
    'review_window' => env('VERIFICATION_REVIEW_WINDOW', '2 business days'),

    // Bump when the consent wording changes; stored with each consent.
    'consent_version' => '2026-10',

    // Days files of rejected, expired and withdrawn submissions are kept before purge.
    'retention_days' => (int) env('VERIFICATION_RETENTION_DAYS', 30),

    // Strip document images of approved submissions once the decision is final?
    'strip_approved_files' => (bool) env('VERIFICATION_STRIP_APPROVED', false),
    'strip_approved_after_days' => (int) env('VERIFICATION_STRIP_APPROVED_AFTER_DAYS', 7),

    // Upload limits (kilobytes) and minimum image size (pixels, long side).
    'identity_max_kb' => 8192,
    'company_max_kb' => 12288,
    'min_image_dimension' => 600,

    // Warn the reviewer when the document expires within this many days.
    'expiring_soon_days' => 90,

    // Reasons a reviewer may pick when rejecting.
    'rejection_reasons' => [
        'unreadable' => 'Document unreadable or too low quality',
        'expired' => 'Document expired or expires too soon',
        'mismatch' => 'Details do not match the document',
        'incomplete' => 'Required pages or documents are missing',
        'unsupported' => 'Document type not accepted',
        'suspicious' => 'We could not verify the document as genuine',
        'other' => 'Other (see message)',
    ],
];
