<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Member network configuration (shared by profiles, networking, messaging,
| voting, verification and plans)
|--------------------------------------------------------------------------
*/

return [

    // Tier slugs ranked from most to least senior. Higher rank = more standing.
    'tier_ranks' => [
        'sovereign-partner' => 3,
        'principal-circle' => 2,
        'corporate-affiliate' => 1,
    ],

    // Members of at least this rank (or any staff admin) may open motions for a vote.
    'motion_creator_min_rank' => 3,

    // Sectors members can pick for their profile and be matched on. The first six
    // mirror the sectors on the public site; the rest cover common supply chains.
    'sectors' => [
        'government-public-sector' => 'Government & Public Sector',
        'energy-natural-resources' => 'Energy & Natural Resources',
        'infrastructure-transportation' => 'Infrastructure & Transportation',
        'defense-security' => 'Defense & Security',
        'technology-innovation' => 'Technology & Innovation',
        'finance-investments' => 'Finance & Investments',
        'agriculture-food' => 'Agriculture & Food',
        'health-life-sciences' => 'Health & Life Sciences',
        'logistics-trade' => 'Logistics & Trade',
        'manufacturing-industry' => 'Manufacturing & Industry',
        'media-communications' => 'Media & Communications',
        'legal-professional-services' => 'Legal & Professional Services',
        'real-estate-construction' => 'Real Estate & Construction',
        'education-research' => 'Education & Research',
    ],

    // Where a member sits in a supply chain / value chain (used for complementary matching).
    'supply_chain_roles' => [
        'producer' => 'Producer / Supplier',
        'manufacturer' => 'Manufacturer / Processor',
        'distributor' => 'Distributor / Logistics',
        'buyer' => 'Buyer / Off-taker',
        'financier' => 'Financier / Investor',
        'technology' => 'Technology Provider',
        'developer' => 'Developer / Contractor',
        'operator' => 'Operator',
        'regulator' => 'Government / Regulator',
        'advisor' => 'Advisor / Professional Services',
    ],

    // Roles that naturally complement each other, used by the matchmaker.
    'complementary_roles' => [
        'producer' => ['manufacturer', 'distributor', 'buyer', 'financier'],
        'manufacturer' => ['producer', 'distributor', 'buyer', 'technology', 'financier'],
        'distributor' => ['producer', 'manufacturer', 'buyer', 'operator'],
        'buyer' => ['producer', 'manufacturer', 'distributor'],
        'financier' => ['developer', 'producer', 'manufacturer', 'operator', 'technology'],
        'technology' => ['manufacturer', 'operator', 'developer', 'financier'],
        'developer' => ['financier', 'operator', 'regulator', 'technology', 'advisor'],
        'operator' => ['developer', 'distributor', 'technology', 'financier'],
        'regulator' => ['developer', 'operator', 'advisor'],
        'advisor' => ['developer', 'regulator', 'financier', 'operator', 'buyer'],
    ],

    // What a member says they are looking for.
    'seeking_kinds' => [
        'partner' => 'Strategic partners',
        'supplier' => 'Suppliers',
        'buyer' => 'Buyers / off-takers',
        'investor' => 'Investors / capital',
        'advisor' => 'Advisors / experts',
        'introductions' => 'Introductions to decision-makers',
    ],

    'profile_visibility' => [
        'members' => 'Visible to verified members',
        'hidden' => 'Hidden from the network',
    ],

    // Identity documents accepted for ID verification.
    'identity_document_types' => [
        'passport' => 'Passport',
        'national_id' => 'National ID card',
        'drivers_licence' => "Driver's licence",
        'residence_permit' => 'Residence permit',
    ],

    // Documents accepted for company verification.
    'company_document_types' => [
        'registration_certificate' => 'Certificate of incorporation / registration',
        'tax_certificate' => 'Tax registration certificate',
        'proof_of_address' => 'Proof of registered address',
        'ownership_chart' => 'Ownership / shareholder structure',
        'authority_letter' => 'Letter of authority for the applicant',
        'licence' => 'Operating licence or permit',
    ],

    // Private (non-public) disk folder for verification uploads.
    'verification_disk' => 'local',
    'verification_path' => 'verification',

    // How long an approved verification stays valid before it must be renewed.
    'verification_valid_months' => 24,
];
