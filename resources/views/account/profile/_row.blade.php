@php
    $sectorOptions = (array) config('network.sectors');
    $n = is_numeric($i) ? $i + 1 : '#';
@endphp
<fieldset class="pf-row" data-row>
    <legend class="pf-row-title">{{ $kind === 'services' ? 'Service' : 'Portfolio item' }} <span data-row-n>{{ $n }}</span></legend>
    <button type="button" class="pf-remove" data-remove hidden aria-label="Remove this {{ $kind === 'services' ? 'service' : 'portfolio item' }}"><x-icon name="x" /></button>
    @if ($kind === 'services')
        <x-profile.field :name="'services['.$i.'][title]'" label="Title" :value="$row['title'] ?? null" max="120" />
        <x-profile.field :name="'services['.$i.'][description]'" label="Description" :value="$row['description'] ?? null" rows="3" max="600" />
        <x-profile.field :name="'services['.$i.'][sector]'" label="Sector" :value="$row['sector'] ?? null" :options="$sectorOptions" />
    @else
        <x-profile.field :name="'portfolio['.$i.'][title]'" label="Title" :value="$row['title'] ?? null" max="120" />
        <x-profile.field :name="'portfolio['.$i.'][summary]'" label="Summary" :value="$row['summary'] ?? null" rows="3" max="600" />
        <div class="pf-two">
            <x-profile.field :name="'portfolio['.$i.'][year]'" label="Year" type="number" :value="$row['year'] ?? null" />
            <x-profile.field :name="'portfolio['.$i.'][client_type]'" label="Client type or sector" :value="$row['client_type'] ?? null" max="120" />
        </div>
        <x-profile.field :name="'portfolio['.$i.'][link]'" label="Link (optional)" type="url" :value="$row['link'] ?? null" placeholder="https://" />
    @endif
</fieldset>
