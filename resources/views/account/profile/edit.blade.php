@php
    $sectorMap = (array) config('network.sectors');
    $roleMap = (array) config('network.supply_chain_roles');
    $kindMap = (array) config('network.seeking_kinds');
    $visMap = (array) config('network.profile_visibility');
    $failed = old('section');
    $list = fn (string $section, string $key, ?array $current) => $failed === $section ? (array) old($key, []) : ($current ?? []);
    $missing = collect($checklist)->where('done', false)->values();
    $sections = [
        'identity' => 'Identity', 'about' => 'About', 'location' => 'Location', 'focus' => 'Sectors & role',
        'seeking' => 'Seeking / offering', 'services' => 'Services', 'portfolio' => 'Portfolio',
        'organisation' => 'Organisation', 'links' => 'Links', 'visibility' => 'Visibility',
    ];
    $rowsFor = function (string $section, ?array $saved) use ($failed) {
        $rows = $failed === $section ? array_values((array) old($section, [])) : array_values($saved ?? []);

        return array_pad($rows, min(\App\Services\ProfileService::MAX_ROWS, max(3, count($rows) + 2)), []);
    };
    $languages = $failed === 'location' ? (is_array(old('languages')) ? implode(', ', old('languages')) : old('languages')) : implode(', ', $profile->languages ?? []);
@endphp

<x-account.shell title="My Profile" active="profile">
    <header class="ac-top">
        <div>
            <p class="ac-eyebrow">Member Network</p>
            <h1>My Profile</h1>
            <p class="ac-sub">A complete profile is how other members find, match with and trust you.</p>
        </div>
        <a href="#preview" class="ac-btn"><x-icon name="eye" />Preview as others see me</a>
    </header>

    @if (session('status'))
        <p class="ac-flash ac-flash-ok" role="status"><x-icon name="check-circle" />{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <p class="ac-flash ac-flash-warn" role="alert">Some details need attention. Review the highlighted fields below.</p>
    @endif

    @if (! $canUseNetwork)
        <div class="ac-flash ac-flash-warn" role="note">
            <p>You can build your profile now. To appear in the member directory you also need an approved membership and a verified email, and then identity verification.</p>
        </div>
    @elseif (! $identityVerified)
        <div class="ac-flash ac-flash-warn" role="note">
            <p>Your profile is not listed in the directory yet: listing also requires identity verification.</p>
            @if (\Illuminate\Support\Facades\Route::has('verification.index'))
                <a href="{{ route('verification.index') }}" class="ac-btn">Verify identity</a>
            @endif
        </div>
    @endif

    {{-- Compact completeness bar (phones) --}}
    <details class="pf-bar" id="checklist-m">
        <summary>
            <span class="pf-bar-top"><b>{{ $score }}% complete</b><x-icon name="chevron-down" /></span>
            <span class="pf-track" aria-hidden="true"><i style="width: {{ $score }}%"></i></span>
        </summary>
        @include('account.profile._checklist', ['missing' => $missing, 'score' => $score])
    </details>

    <nav class="pf-nav" aria-label="Profile sections">
        @foreach ($sections as $key => $label)
            <a href="#{{ $key }}">{{ $label }}</a>
        @endforeach
        <a href="#preview">Preview</a>
    </nav>

    <div class="pf-layout">
        <div class="pf-main">

            {{-- 1 Identity --}}
            <section class="ac-panel pf-sec" id="identity" aria-labelledby="h-identity">
                <p class="ac-eyebrow">1 of 10</p>
                <h2 id="h-identity">Identity &amp; headline</h2>

                <div class="pf-avatar-row">
                    @if ($profile->avatarUrl())
                        <img class="pc-avatar pf-avatar" src="{{ $profile->avatarUrl() }}" alt="Your current profile photo" width="96" height="96">
                    @else
                        <span class="pc-avatar pc-initials pf-avatar" aria-hidden="true">{{ app(\App\Services\ProfileService::class)->initials($profile->display_name ?: $profile->user->name) }}</span>
                    @endif
                    <div class="pf-avatar-actions">
                        <form method="POST" action="{{ route('account.profile.avatar') }}" enctype="multipart/form-data" class="ac-stack">
                            @csrf
                            <div class="ac-field">
                                <label for="avatar" class="ac-label">Profile photo</label>
                                <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" class="ac-input pf-file" aria-describedby="avatar-hint @error('avatar') avatar-err @enderror" @error('avatar') aria-invalid="true" @enderror>
                                <p id="avatar-hint" class="pf-hint">JPG, PNG or WebP, up to 3 MB. Cropped to a square.</p>
                                @error('avatar')<p id="avatar-err" class="ac-err">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="ac-btn ac-btn-solid">Upload photo</button>
                        </form>
                        @if ($profile->avatar_path)
                            <form method="POST" action="{{ route('account.profile.avatar.destroy') }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="ac-btn">Remove photo</button>
                            </form>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('account.profile.update') }}" class="ac-stack" novalidate>
                    @csrf @method('PUT')
                    <input type="hidden" name="section" value="identity">
                    <x-profile.field name="display_name" label="Display name" :value="$profile->display_name" max="80" required autocomplete="name" hint="Shown to other members instead of your account name." />
                    <x-profile.field name="headline" label="Headline" :value="$profile->headline" max="160" placeholder="e.g. Infrastructure financier connecting capital to African energy projects" hint="One line, up to 160 characters." />
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save identity</button>
                </form>
            </section>

            {{-- 2 About --}}
            <section class="ac-panel pf-sec" id="about" aria-labelledby="h-about">
                <p class="ac-eyebrow">2 of 10</p>
                <h2 id="h-about">About</h2>
                <form method="POST" action="{{ route('account.profile.update') }}" class="ac-stack" novalidate>
                    @csrf @method('PUT')
                    <input type="hidden" name="section" value="about">
                    <x-profile.field name="bio" label="Bio" :value="$profile->bio" rows="7" max="2000" hint="At least 120 characters counts toward completeness. Say who you are, what you have built and how you work. Up to 2000 characters." />
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save bio</button>
                </form>
            </section>

            {{-- 3 Location & languages --}}
            <section class="ac-panel pf-sec" id="location" aria-labelledby="h-location">
                <p class="ac-eyebrow">3 of 10</p>
                <h2 id="h-location">Location &amp; languages</h2>
                <form method="POST" action="{{ route('account.profile.update') }}" class="ac-stack" novalidate>
                    @csrf @method('PUT')
                    <input type="hidden" name="section" value="location">
                    <div class="pf-two">
                        <x-profile.field name="city" label="City" :value="$profile->city" max="100" autocomplete="address-level2" />
                        <x-profile.field name="country" label="Country" :value="$profile->country" max="100" autocomplete="country-name" />
                    </div>
                    <div class="ac-field pf-field">
                        <label for="languages" class="ac-label">Languages</label>
                        <input type="text" id="languages" name="languages" value="{{ $languages }}" class="ac-input" placeholder="English, French, Arabic" aria-describedby="languages-hint @error('languages') languages-err @enderror" @error('languages') aria-invalid="true" @enderror>
                        <p id="languages-hint" class="pf-hint">Separate with commas. Up to 10.</p>
                        @error('languages')<p id="languages-err" class="ac-err">{{ $message }}</p>@enderror
                        @error('languages.*')<p class="ac-err">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save location</button>
                </form>
            </section>

            {{-- 4 Sectors and supply-chain role --}}
            <section class="ac-panel pf-sec" id="focus" aria-labelledby="h-focus">
                <p class="ac-eyebrow">4 of 10</p>
                <h2 id="h-focus">Sectors &amp; supply-chain role</h2>
                <form method="POST" action="{{ route('account.profile.update') }}" class="ac-stack" novalidate>
                    @csrf @method('PUT')
                    <input type="hidden" name="section" value="focus">
                    @php $selSectors = $list('focus', 'sectors', $profile->sectors); $selRoles = $list('focus', 'supply_chain_roles', $profile->supply_chain_roles); @endphp
                    <fieldset class="pf-set" aria-describedby="sectors-hint @error('sectors') sectors-err @enderror">
                        <legend class="ac-label">Sectors you work in</legend>
                        <p id="sectors-hint" class="pf-hint">Pick all that apply. At least one is needed for matching.</p>
                        <div class="pf-chips">
                            @foreach ($sectorMap as $value => $label)
                                <label class="pf-chip"><input type="checkbox" name="sectors[]" value="{{ $value }}" @checked(in_array($value, $selSectors, true))><span>{{ $label }}</span></label>
                            @endforeach
                        </div>
                        @error('sectors')<p id="sectors-err" class="ac-err">{{ $message }}</p>@enderror
                        @error('sectors.*')<p class="ac-err">{{ $message }}</p>@enderror
                    </fieldset>
                    <fieldset class="pf-set" aria-describedby="roles-hint @error('supply_chain_roles') roles-err @enderror">
                        <legend class="ac-label">Where you sit in the supply chain</legend>
                        <p id="roles-hint" class="pf-hint">Used to suggest complementary partners.</p>
                        <div class="pf-chips">
                            @foreach ($roleMap as $value => $label)
                                <label class="pf-chip"><input type="checkbox" name="supply_chain_roles[]" value="{{ $value }}" @checked(in_array($value, $selRoles, true))><span>{{ $label }}</span></label>
                            @endforeach
                        </div>
                        @error('supply_chain_roles')<p id="roles-err" class="ac-err">{{ $message }}</p>@enderror
                        @error('supply_chain_roles.*')<p class="ac-err">{{ $message }}</p>@enderror
                    </fieldset>
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save sectors &amp; role</button>
                </form>
            </section>

            {{-- 5 Seeking / offering --}}
            <section class="ac-panel pf-sec" id="seeking" aria-labelledby="h-seeking">
                <p class="ac-eyebrow">5 of 10</p>
                <h2 id="h-seeking">Seeking &amp; offering</h2>
                <form method="POST" action="{{ route('account.profile.update') }}" class="ac-stack" novalidate>
                    @csrf @method('PUT')
                    <input type="hidden" name="section" value="seeking">
                    @php $selKinds = $list('seeking', 'seeking_kinds', $profile->seeking_kinds); @endphp
                    <fieldset class="pf-set" aria-describedby="kinds-hint @error('seeking_kinds') kinds-err @enderror">
                        <legend class="ac-label">I am looking for</legend>
                        <p id="kinds-hint" class="pf-hint">Pick what you want from the network.</p>
                        <div class="pf-chips">
                            @foreach ($kindMap as $value => $label)
                                <label class="pf-chip"><input type="checkbox" name="seeking_kinds[]" value="{{ $value }}" @checked(in_array($value, $selKinds, true))><span>{{ $label }}</span></label>
                            @endforeach
                        </div>
                        @error('seeking_kinds')<p id="kinds-err" class="ac-err">{{ $message }}</p>@enderror
                        @error('seeking_kinds.*')<p class="ac-err">{{ $message }}</p>@enderror
                    </fieldset>
                    <x-profile.field name="seeking_summary" label="What I am looking for" :value="$profile->seeking_summary" rows="4" max="1000" />
                    <x-profile.field name="offering_summary" label="What I offer" :value="$profile->offering_summary" rows="4" max="1000" />
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save seeking &amp; offering</button>
                </form>
            </section>

            {{-- 6 Services --}}
            <section class="ac-panel pf-sec" id="services" aria-labelledby="h-services">
                <p class="ac-eyebrow">6 of 10</p>
                <h2 id="h-services">Services</h2>
                <p class="ac-lead">What you can deliver for other members. List between 1 and {{ \App\Services\ProfileService::MAX_ROWS }}.</p>
                <form method="POST" action="{{ route('account.profile.update') }}" class="ac-stack" novalidate data-rows="services">
                    @csrf @method('PUT')
                    <input type="hidden" name="section" value="services">
                    @error('services')<p class="ac-err" role="alert">{{ $message }}</p>@enderror
                    <div class="pf-rows" data-rows-list data-max="{{ \App\Services\ProfileService::MAX_ROWS }}">
                        @foreach ($rowsFor('services', $profile->services) as $i => $row)
                            @include('account.profile._row', ['kind' => 'services', 'i' => $i, 'row' => $row])
                        @endforeach
                    </div>
                    <template data-row-template>@include('account.profile._row', ['kind' => 'services', 'i' => '__i__', 'row' => []])</template>
                    <button type="button" class="ac-btn pf-add" data-add hidden><x-icon name="plus" />Add another service</button>
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save services</button>
                </form>
            </section>

            {{-- 7 Portfolio --}}
            <section class="ac-panel pf-sec" id="portfolio" aria-labelledby="h-portfolio">
                <p class="ac-eyebrow">7 of 10</p>
                <h2 id="h-portfolio">Portfolio</h2>
                <p class="ac-lead">Notable work that builds trust. Up to {{ \App\Services\ProfileService::MAX_ROWS }} items.</p>
                <form method="POST" action="{{ route('account.profile.update') }}" class="ac-stack" novalidate data-rows="portfolio">
                    @csrf @method('PUT')
                    <input type="hidden" name="section" value="portfolio">
                    @error('portfolio')<p class="ac-err" role="alert">{{ $message }}</p>@enderror
                    <div class="pf-rows" data-rows-list data-max="{{ \App\Services\ProfileService::MAX_ROWS }}">
                        @foreach ($rowsFor('portfolio', $profile->portfolio) as $i => $row)
                            @include('account.profile._row', ['kind' => 'portfolio', 'i' => $i, 'row' => $row])
                        @endforeach
                    </div>
                    <template data-row-template>@include('account.profile._row', ['kind' => 'portfolio', 'i' => '__i__', 'row' => []])</template>
                    <button type="button" class="ac-btn pf-add" data-add hidden><x-icon name="plus" />Add another item</button>
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save portfolio</button>
                </form>
            </section>

            {{-- 8 Organisation --}}
            <section class="ac-panel pf-sec" id="organisation" aria-labelledby="h-organisation">
                <p class="ac-eyebrow">8 of 10</p>
                <h2 id="h-organisation">Organisation</h2>
                <form method="POST" action="{{ route('account.profile.update') }}" class="ac-stack" novalidate>
                    @csrf @method('PUT')
                    <input type="hidden" name="section" value="organisation">
                    <x-profile.field name="organisation_name" label="Organisation name" :value="$profile->organisation_name" max="160" autocomplete="organization" />
                    <div class="pf-two">
                        <x-profile.field name="organisation_role" label="Your role" :value="$profile->organisation_role" max="120" autocomplete="organization-title" />
                        <x-profile.field name="organisation_size" label="Organisation size" :value="$profile->organisation_size" max="60" placeholder="e.g. 11-50 people" />
                    </div>
                    <x-profile.field name="organisation_website" label="Organisation website" type="url" :value="$profile->organisation_website" placeholder="https://" />
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save organisation</button>
                </form>
            </section>

            {{-- 9 Links --}}
            <section class="ac-panel pf-sec" id="links" aria-labelledby="h-links">
                <p class="ac-eyebrow">9 of 10</p>
                <h2 id="h-links">Links</h2>
                <form method="POST" action="{{ route('account.profile.update') }}" class="ac-stack" novalidate>
                    @csrf @method('PUT')
                    <input type="hidden" name="section" value="links">
                    <x-profile.field name="website" label="Website" type="url" :value="$profile->website" placeholder="https://" />
                    <x-profile.field name="linkedin_url" label="LinkedIn" type="url" :value="$profile->linkedin_url" placeholder="https://www.linkedin.com/in/..." />
                    <x-profile.field name="public_email" label="Public contact email" type="email" :value="$profile->public_email" hint="Optional. Shown to other members. Your sign-in email is never shown." />
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save links</button>
                </form>
            </section>

            {{-- 10 Visibility --}}
            <section class="ac-panel pf-sec" id="visibility" aria-labelledby="h-visibility">
                <p class="ac-eyebrow">10 of 10</p>
                <h2 id="h-visibility">Visibility</h2>
                <p class="ac-lead">Appearing in the member directory requires an approved membership and a verified email, plus completed identity verification. Choosing "visible" only makes you eligible; it does not bypass verification. Hidden profiles never appear.</p>
                <form method="POST" action="{{ route('account.profile.update') }}" class="ac-stack" novalidate>
                    @csrf @method('PUT')
                    <input type="hidden" name="section" value="visibility">
                    <fieldset class="pf-set" @error('visibility') aria-describedby="vis-err" @enderror>
                        <legend class="ac-label">Who can see my profile</legend>
                        @foreach ($visMap as $value => $label)
                            <label class="pf-radio"><input type="radio" name="visibility" value="{{ $value }}" @checked(old('visibility', $profile->visibility) === $value)><span>{{ $label }}</span></label>
                        @endforeach
                        @error('visibility')<p id="vis-err" class="ac-err">{{ $message }}</p>@enderror
                    </fieldset>
                    <label class="pf-radio pf-toggle">
                        <input type="hidden" name="open_to_collaboration" value="0">
                        <input type="checkbox" name="open_to_collaboration" value="1" @checked((bool) (old('section') === 'visibility' ? old('open_to_collaboration') : $profile->open_to_collaboration))>
                        <span>I am open to collaboration requests</span>
                    </label>
                    <button type="submit" class="ac-btn ac-btn-solid pf-save">Save visibility</button>
                </form>
            </section>

            {{-- Preview --}}
            <section class="ac-panel pf-sec" id="preview" aria-labelledby="h-preview">
                <p class="ac-eyebrow">Preview</p>
                <h2 id="h-preview">How others see me</h2>
                <p class="ac-lead">This is the card other members see in the directory. Reload after saving to refresh it.</p>
                <x-profile-card :profile="$profile" />
            </section>
        </div>

        <aside class="pf-aside" aria-label="Profile completeness">
            <div class="ac-panel pf-sticky" id="checklist">
                <div class="ac-ringwrap">
                    <div class="ac-ring" role="img" aria-label="{{ $score }} percent complete">
                        <svg width="120" height="120" viewBox="0 0 120 120" aria-hidden="true">
                            <circle class="tr" cx="60" cy="60" r="50" pathLength="100"/>
                            <circle class="pr" cx="60" cy="60" r="50" pathLength="100" style="--off: {{ 100 - $score }}"/>
                        </svg>
                        <b>{{ $score }}%</b>
                    </div>
                    <p>{{ $score >= 80 ? 'Your profile is complete enough to be matched.' : 'Reach 80% to be treated as a complete profile.' }}</p>
                </div>
                @include('account.profile._checklist', ['missing' => $missing, 'score' => $score])
            </div>
        </aside>
    </div>

    <script>
        (function () {
            document.querySelectorAll('[data-rows]').forEach(function (form) {
                var list = form.querySelector('[data-rows-list]');
                var tpl = form.querySelector('[data-row-template]');
                var add = form.querySelector('[data-add]');
                var max = parseInt(list.getAttribute('data-max'), 10);
                var next = list.children.length;
                var visible = function () { return list.querySelectorAll('[data-row]:not([hidden])').length; };
                var renumber = function () {
                    var n = 0;
                    list.querySelectorAll('[data-row]:not([hidden])').forEach(function (r) { n++; r.querySelector('[data-row-n]').textContent = n; });
                    add.disabled = visible() >= max;
                };
                add.hidden = false;
                list.querySelectorAll('[data-remove]').forEach(function (b) { b.hidden = false; });
                add.addEventListener('click', function () {
                    if (visible() >= max) { return; }
                    var holder = document.createElement('div');
                    holder.innerHTML = tpl.innerHTML.replace(/__i__/g, next++);
                    var row = holder.firstElementChild;
                    row.querySelector('[data-remove]').hidden = false;
                    list.appendChild(row);
                    renumber();
                    var first = row.querySelector('input, textarea, select');
                    if (first) { first.focus(); }
                });
                list.addEventListener('click', function (e) {
                    var btn = e.target.closest('[data-remove]');
                    if (!btn) { return; }
                    var row = btn.closest('[data-row]');
                    row.querySelectorAll('input, textarea, select').forEach(function (f) { f.value = ''; });
                    row.hidden = true;
                    renumber();
                    add.focus();
                });
                renumber();
            });
        })();
    </script>
</x-account.shell>
