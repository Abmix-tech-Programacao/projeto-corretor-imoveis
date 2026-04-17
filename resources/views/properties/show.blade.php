@extends('layouts.app')

@section('title', $property->title.' | Chave na Mão')

@section('content')
    @php
        $whatsMessage = rawurlencode('Ola! Tenho interesse no imovel '.$property->title.'.');
    @endphp
    <section class="section">
        <div class="container details-layout">
            <div class="details-main">
                <div class="gallery-card" data-gallery>
                    <img src="{{ $property->cover_image }}" alt="{{ $property->title }}" data-gallery-main>
                    <div class="thumb-row">
                        <button type="button" class="thumb is-active" data-gallery-item="{{ $property->cover_image }}">
                            <img src="{{ $property->cover_image }}" alt="Capa">
                        </button>
                        @foreach ($property->images as $image)
                            <button type="button" class="thumb" data-gallery-item="{{ $image->path }}">
                                <img src="{{ $image->path }}" alt="{{ $image->caption ?? $property->title }}">
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="details-header">
                    <span class="property-badge">{{ $property->property_type }}</span>
                    <h1>{{ $property->title }}</h1>
                    <p>{{ $property->address }} - {{ $property->neighborhood }}, {{ $property->city }}/{{ $property->state }}</p>
                </div>

                <div class="metrics">
                    <div><strong>{{ $property->bedrooms }}</strong><small>Dormitórios</small></div>
                    <div><strong>{{ $property->bathrooms }}</strong><small>Banheiros</small></div>
                    <div><strong>{{ $property->parking_spaces }}</strong><small>Vagas</small></div>
                    <div><strong>{{ $property->area ?: '-' }}</strong><small>Área m2</small></div>
                </div>

                <article class="content-card">
                    <h2>Descrição</h2>
                    <p>{{ $property->description }}</p>
                </article>

                @if ($property->feature_list)
                    <article class="content-card">
                        <h2>Diferenciais</h2>
                        <ul class="feature-list">
                            @foreach ($property->feature_list as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endif

                @if ($property->latitude && $property->longitude)
                    <article class="content-card">
                        <h2>Mapa</h2>
                        <iframe
                            title="Mapa de localização"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.openstreetmap.org/export/embed.html?bbox={{ $property->longitude - 0.01 }}%2C{{ $property->latitude - 0.01 }}%2C{{ $property->longitude + 0.01 }}%2C{{ $property->latitude + 0.01 }}&layer=mapnik&marker={{ $property->latitude }}%2C{{ $property->longitude }}">
                        </iframe>
                    </article>
                @endif
            </div>

            <aside class="details-sidebar">
                <div class="broker-inline">
                    <small>Corretor responsavel</small>
                    <strong>Euclides</strong>
                    <div class="broker-contact-actions">
                        <a class="broker-contact-link is-whatsapp" href="https://wa.me/5511983775679?text={{ $whatsMessage }}" target="_blank" rel="noopener noreferrer">
                            Falar no WhatsApp
                        </a>
                        <a class="broker-contact-link" href="mailto:euclides@chavenamao.company">
                            Enviar e-mail
                        </a>
                    </div>
                </div>
                <div class="contact-card">
                    <p class="price">{{ $property->formatted_price }}</p>
                    <small>Código {{ $property->code }}</small>
                    <form action="{{ route('leads.store') }}" method="POST" class="lead-form">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <input type="hidden" name="source_page" value="{{ request()->fullUrl() }}">
                        <label>
                            Nome
                            <input type="text" name="name" value="{{ old('name') }}" required>
                        </label>
                        <label>
                            Email
                            <input type="email" name="email" value="{{ old('email') }}" required>
                        </label>
                        <label>
                            Telefone
                            <input type="text" name="phone" value="{{ old('phone') }}" required>
                        </label>
                        <label>
                            Mensagem
                            <textarea name="message" rows="4">{{ old('message', 'Quero mais informações sobre este imóvel.') }}</textarea>
                        </label>
                        <button class="btn btn-primary w-full" type="submit">Quero atendimento</button>
                    </form>
                </div>
            </aside>
        </div>
    </section>

    <section class="section section-alt">
        <div class="container">
            <div class="section-head">
                <h2>Imóveis relacionados</h2>
            </div>
            <div class="property-grid compact">
                @foreach ($relatedProperties as $relatedProperty)
                    @include('partials.property-card', ['property' => $relatedProperty])
                @endforeach
            </div>
        </div>
    </section>
@endsection
