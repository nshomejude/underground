@props(['offices'])

@php
    /*
     * HD globe atlas (canvas, resources/js/globe.js). The office list, legend and
     * controls are real HTML so the section is useful without JavaScript; the
     * canvas only appears once the script runs. Admin switch:
     * Configuration > Visual Effects > globe_atlas_enabled.
     */
    $enabled = app(\Domain\Content\Repositories\SiteSettingRepository::class)->current()->globeAtlasEnabled;

    $points = collect($offices)->map(fn ($o) => [
        'id' => $o['id'],
        'name' => $o['city'],
        'tag' => $o['note'],
        'hq' => $o['id'] === 'dc',
        'c' => [$o['lng'], $o['lat']],
        'txt' => $o['blurb'],
        'lp' => ['abj' => 'bl', 'los' => 'tc', 'dla' => 'br'][$o['id']] ?? 'r',
    ])->values();

    $links = [['dc', 'par'], ['par', 'abj'], ['abj', 'los'], ['los', 'dla'], ['dc', 'los']];
@endphp

@if ($enabled)
    <section
        class="gl border-b border-border bg-ink"
        data-globe
        data-offices="{{ $points->toJson() }}"
        data-links="{{ json_encode($links) }}"
        aria-labelledby="gl-heading"
    >
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
            <x-section-heading eyebrow="Our Network">
                <span id="gl-heading">One network. Five offices.</span>
            </x-section-heading>

            <p class="mt-5 max-w-2xl text-base leading-relaxed text-body">
                From Washington, D.C. to the Gulf of Guinea, Underground connects capital, institutions and people.
                Drag the globe, zoom, or choose an office to fly there.
            </p>

            <div class="gl-layout mt-10">
                <div class="gl-stage" data-globe-stage>
                    <canvas
                        class="gl-canvas"
                        data-globe-canvas
                        tabindex="0"
                        role="img"
                        aria-label="Interactive globe showing Underground Network offices in Washington D.C., Paris, Abidjan, Lagos and Douala. Arrow keys rotate the globe, plus and minus zoom."
                    ></canvas>
                    <div class="gl-hint" aria-hidden="true">Drag &middot; Scroll to zoom &middot; Click an office</div>
                    <div class="gl-card" data-globe-card role="status" aria-live="polite" hidden></div>
                    <noscript><x-reach-map class="absolute inset-0 m-auto h-auto w-4/5" /></noscript>
                </div>

                <aside class="gl-panel" aria-label="Offices">
                    <h3>Our offices</h3>
                    <ul class="gl-offices" data-globe-list>
                        @foreach ($points as $point)
                            <li @class(['is-hq' => $point['hq']])>
                                <button type="button" data-id="{{ $point['id'] }}" aria-pressed="false">
                                    <span class="gl-dot"></span>
                                    <span class="gl-n">{{ $point['name'] }}</span>
                                    <span class="gl-s">{{ $point['tag'] }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <h3>Legend</h3>
                    <div class="gl-legend">
                        <div><i class="gl-l-hq"></i>Headquarters</div>
                        <div><i class="gl-l-of"></i>Regional office</div>
                        <div><i class="gl-l-arc"></i>Network link</div>
                        <div><i class="gl-l-pk"></i>Live exchange</div>
                    </div>

                    <div class="gl-controls">
                        <button type="button" class="gl-btn" data-globe-pause aria-pressed="false">Pause rotation</button>
                        <button type="button" class="gl-btn" data-globe-reset>Reset view</button>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endif
