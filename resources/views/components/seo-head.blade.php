@props([
    'title' => null,
    'description' => null,
    'type' => 'website',
    'image' => null,
    'schema' => [],
    'siteSetting',
])

@php
    $routeName = optional(request()->route())->getName();
    $siteName = config('seo.site_name');
    $isHome = $routeName === 'home' || request()->path() === '/';
    $isError = $routeName === null && ! $isHome;

    $fullTitle = $isHome
        ? ($siteSetting->metaTitle && $siteSetting->metaTitle !== 'Underground Network' ? $siteSetting->metaTitle : config('seo.default_title'))
        : ($title ? $title.' — '.$siteName : $siteName);

    $metaDescription = $description
        ?? config('seo.descriptions.'.$routeName)
        ?? ($siteSetting->metaDescription ?: config('seo.default_description'));
    $metaDescription = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $metaDescription))), 200, '…');

    $noindex = $isError || in_array($routeName, config('seo.noindex'), true) || request()->is('admin*');
    $canonical = url()->current();
    $imageUrl = $image ?: ($siteSetting->ogImageUrl ?: asset(config('seo.og_image')));
    $org = config('seo.organization');

    $organization = [
        '@type' => ['Organization', 'ProfessionalService'],
        '@id' => url('/').'#organization',
        'name' => $siteName,
        'legalName' => $org['legal_name'],
        'url' => url('/'),
        'logo' => asset('images/og-default.png'),
        'image' => $imageUrl,
        'description' => config('seo.default_description'),
        'email' => $org['email'],
        'telephone' => $org['telephone'],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $org['street'],
            'addressLocality' => $org['city'],
            'addressRegion' => $org['region'],
            'postalCode' => $org['postal_code'],
            'addressCountry' => $org['country'],
        ],
        'areaServed' => $org['area_served'],
        'knowsAbout' => $org['knows_about'],
        'contactPoint' => [[
            '@type' => 'ContactPoint',
            'contactType' => 'customer service',
            'email' => $org['email'],
            'telephone' => $org['telephone'],
            'availableLanguage' => ['English', 'French'],
        ]],
    ];

    if (! empty($siteSetting->socialLinks)) {
        $organization['sameAs'] = array_values(array_filter(array_map(
            static fn ($link) => $link['url'] ?? null,
            $siteSetting->socialLinks,
        )));
    }

    $graph = [
        $organization,
        [
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'url' => url('/'),
            'name' => $siteName,
            'description' => config('seo.default_description'),
            'publisher' => ['@id' => url('/').'#organization'],
            'inLanguage' => 'en',
        ],
        [
            '@type' => 'WebPage',
            '@id' => $canonical.'#webpage',
            'url' => $canonical,
            'name' => $fullTitle,
            'description' => $metaDescription,
            'isPartOf' => ['@id' => url('/').'#website'],
            'about' => ['@id' => url('/').'#organization'],
            'inLanguage' => 'en',
        ],
    ];

    // Breadcrumb trail derived from the URL path.
    if (! $isHome && ! $isError) {
        $crumbs = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')]];
        $path = '';
        $segments = request()->segments();
        foreach ($segments as $index => $segment) {
            $path .= '/'.$segment;
            $isLast = $index === array_key_last($segments);
            $crumbs[] = [
                '@type' => 'ListItem',
                'position' => count($crumbs) + 1,
                'name' => config('seo.breadcrumb_labels.'.$segment) ?? ($isLast && $title ? $title : ucwords(str_replace('-', ' ', $segment))),
                'item' => url($path),
            ];
        }
        $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => $crumbs];
    }

    foreach ($schema as $node) {
        $graph[] = $node;
    }

    $jsonLd = ['@context' => 'https://schema.org', '@graph' => $graph];
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="{{ $noindex ? 'noindex,nofollow' : 'index,follow,max-image-preview:large,max-snippet:-1' }}">
<link rel="canonical" href="{{ $canonical }}">
<meta name="theme-color" content="#0B0B0C">
<meta name="author" content="{{ $siteName }}">

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="en_US">
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $imageUrl }}">
<meta property="og:image:alt" content="{{ $siteName }} — Power Beneath the Surface">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $imageUrl }}">
@if ($siteSetting->twitterHandle)
    <meta name="twitter:site" content="{{ $siteSetting->twitterHandle }}">
@endif

<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
