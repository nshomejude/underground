<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| SEO / answer-engine metadata
|--------------------------------------------------------------------------
|
| Per-route meta descriptions (aim for 120–160 characters, written as a
| direct answer to what the page is). Detail pages pass their own
| description to <x-layout>. Routes in `noindex` are kept out of search
| results and the sitemap.
|
*/

return [

    'site_name' => 'Underground Network',

    'default_title' => 'Underground Network | Global Strategic Advisory & Influence Firm',

    'default_description' => 'Underground is a global strategic advisory and influence firm working across government, business, capital and media. Discreet counsel from Washington, D.C., Douala, Abidjan, Lagos and Paris.',

    'og_image' => 'images/og-default.png',

    'organization' => [
        'legal_name' => 'Underground Network Inc.',
        'email' => 'info@un-der.com',
        'telephone' => '+1-571-508-9170',
        'street' => '200 Massachusetts Ave NW',
        'city' => 'Washington',
        'region' => 'DC',
        'postal_code' => '20001',
        'country' => 'US',
        'area_served' => ['United States', 'Cameroon', "Côte d'Ivoire", 'Nigeria', 'France'],
        'knows_about' => [
            'Government affairs',
            'Political risk analysis',
            'International relations and diplomacy',
            'Public-private partnerships',
            'Infrastructure advisory',
            'Investment and capital strategy',
            'Strategic communications',
        ],
    ],

    'descriptions' => [
        'home' => 'Underground is a global strategic advisory and influence firm. We help governments, institutions and investors achieve outcomes through discreet, high-conviction counsel.',
        'about' => 'Learn who Underground is: a deliberately small, partner-led advisory firm founded on discretion, trust and results across government, capital and media.',
        'team' => 'Meet the partners of Underground, a small group of principals who personally lead every mandate across government affairs, intelligence, capital and communications.',
        'contact' => 'Contact Underground: offices in Washington, D.C., Douala, Abidjan, Lagos and Paris, departmental mailboxes and a confidential inquiry channel.',
        'partners' => 'How Underground works with a vetted circle of advisory firms, financial institutions, multilaterals and technology partners, always with discretion.',
        'collaboration' => 'How Underground collaborates with clients: embedded teams, joint working groups, secure channels and a clear engagement cadence from onboarding to close.',
        'portfolio' => 'Selected, anonymised Underground engagements across government, energy, infrastructure, defense, technology and finance, with the outcomes delivered.',
        'projects' => 'Standing Underground initiatives in progress: government transitions, transition finance, technology governance, corridor infrastructure and capital realignment.',
        'events' => 'Underground’s invitation-only roundtables, forums and briefings for sovereign principals, allocators, officials and operators.',
        'careers' => 'Careers at Underground: how we hire discreet, globally minded, high-judgment people for confidential strategic advisory work.',
        'terms' => 'The terms of service governing use of the Underground Network website and its services.',
        'privacy' => 'How Underground collects, uses and protects personal information on this website.',
        'insights.index' => 'Original analysis from Underground on geopolitics, capital flows, government affairs, infrastructure and the quiet mechanics of influence.',
        'capabilities.index' => 'Underground’s eight capabilities: government affairs, diplomacy, intelligence, capital strategy, partnerships, narrative management, PPP advisory and think-tank work.',
        'sectors.index' => 'The six sectors Underground serves: government, energy, infrastructure, defense and security, technology, and finance and investments.',
        'engagement-models.index' => 'Four ways to engage Underground: advisory retainers, project engagements, government affairs support and crisis management.',
        'global-reach' => 'Underground’s global reach: headquarters in Washington, D.C. with offices in Douala, Abidjan, Lagos and Paris serving North America, Africa and Europe.',
        'membership.index' => 'Underground membership tiers for sovereign partners, principals and corporate affiliates, reviewed personally by a partner before any tier is granted.',
        'membership.cards' => 'A preview of the Underground membership cards extended to approved members.',
        'inquiries.create' => 'Submit a confidential inquiry to Underground. A partner reviews every inquiry personally and nothing you share leaves the firm.',
    ],

    'noindex' => [
        'register', 'login', 'password.request', 'password.reset', 'password.email', 'password.update',
        'verification.notice', 'verification.verify', 'verification.send',
        'account.show', 'account.settings', 'account.security', 'account.applications', 'account.documents', 'account.certificate', 'verify.show', 'inquiries.track', 'membership.track', 'membership.apply',
    ],

    // Human labels for breadcrumb trails, keyed by URL segment.
    'breadcrumb_labels' => [
        'about' => 'About',
        'team' => 'Team',
        'contact' => 'Contact',
        'partners' => 'Partners',
        'collaboration' => 'Collaboration',
        'portfolio' => 'Portfolio',
        'projects' => 'Projects',
        'events' => 'Events',
        'careers' => 'Careers',
        'terms' => 'Terms of Service',
        'privacy' => 'Privacy Policy',
        'insights' => 'Insights',
        'capabilities' => 'Capabilities',
        'sectors' => 'Sectors',
        'engagement-models' => 'Engagement Models',
        'global-reach' => 'Global Reach',
        'membership' => 'Membership',
        'confidential-inquiry' => 'Confidential Inquiry',
    ],

];
