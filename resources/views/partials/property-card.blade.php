<article class="property-card">
    <a class="property-card-image" href="{{ route('properties.show', $property) }}">
        <img src="{{ $property->cover_image }}" alt="{{ $property->title }}">
        <span class="property-badge">{{ $property->property_type }}</span>
    </a>
    <div class="property-card-body">
        <h3><a href="{{ route('properties.show', $property) }}">{{ $property->title }}</a></h3>
        <p class="property-location">{{ $property->neighborhood }} - {{ $property->city }}/{{ $property->state }}</p>
        <div class="property-meta">
            <span>{{ $property->bedrooms }} dorm</span>
            <span>{{ $property->bathrooms }} banh</span>
            <span>{{ $property->area ? $property->area.' m2' : 'Área sob consulta' }}</span>
        </div>
        <strong class="property-price">{{ $property->formatted_price }}</strong>
        <a class="btn btn-ghost" href="{{ route('properties.show', $property) }}">Ver detalhes</a>
    </div>
</article>
