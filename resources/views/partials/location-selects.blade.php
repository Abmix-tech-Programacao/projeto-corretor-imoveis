@php
    $locationLabels = $filterOptions['location_labels'] ?? [];
@endphp

<div class="location-filter" data-location-filter data-location-tree='@json($filterOptions['locations'] ?? [])'>
    <div data-location-wrapper="0">
        <label>
            {{ $locationLabels['location'] ?? 'Localização' }}
            <select
                name="location"
                data-location-select="0"
                data-placeholder="{{ $locationLabels['location'] ?? 'Localização' }}"
                data-selected="{{ $filters['location'] ?? '' }}"
            >
                <option value="">{{ $locationLabels['location'] ?? 'Localização' }}</option>
                @foreach (($filterOptions['locations'] ?? []) as $location)
                    <option value="{{ $location['slug'] }}" @selected(($filters['location'] ?? '') === $location['slug'])>
                        {{ $location['name'] }}
                    </option>
                @endforeach
            </select>
        </label>
    </div>

    <div data-location-wrapper="1" hidden>
        <label>
            {{ $locationLabels['child_location'] ?? 'Localização filha' }}
            <select
                name="child_location"
                data-location-select="1"
                data-placeholder="{{ $locationLabels['child_location'] ?? 'Localização filha' }}"
                data-selected="{{ $filters['child_location'] ?? '' }}"
            >
                <option value="">{{ $locationLabels['child_location'] ?? 'Localização filha' }}</option>
            </select>
        </label>
    </div>

    <div data-location-wrapper="2" hidden>
        <label>
            {{ $locationLabels['grandchild_location'] ?? 'Localização neta' }}
            <select
                name="grandchild_location"
                data-location-select="2"
                data-placeholder="{{ $locationLabels['grandchild_location'] ?? 'Localização neta' }}"
                data-selected="{{ $filters['grandchild_location'] ?? '' }}"
            >
                <option value="">{{ $locationLabels['grandchild_location'] ?? 'Localização neta' }}</option>
            </select>
        </label>
    </div>

    <div data-location-wrapper="3" hidden>
        <label>
            {{ $locationLabels['great_grandchild_location'] ?? 'Localização bisneta' }}
            <select
                name="great_grandchild_location"
                data-location-select="3"
                data-placeholder="{{ $locationLabels['great_grandchild_location'] ?? 'Localização bisneta' }}"
                data-selected="{{ $filters['great_grandchild_location'] ?? '' }}"
            >
                <option value="">{{ $locationLabels['great_grandchild_location'] ?? 'Localização bisneta' }}</option>
            </select>
        </label>
    </div>
</div>
