@extends('layouts.app')

@section('title', 'Imoveis | Chave na Mao')

@section('content')
    <section class="section">
        <div class="container listing-layout">
            <aside class="filter-panel">
                <h2>Filtros</h2>
                <form action="{{ route('properties.index') }}" method="GET">
                    @if (!empty($filters['menu_category']))
                        <input type="hidden" name="menu_category" value="{{ $filters['menu_category'] }}">
                    @endif
                    <label>
                        Busca
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Titulo, bairro, cidade">
                    </label>
                    @include('partials.location-selects')
                    <label>
                        Finalidade
                        <select name="purpose">
                            <option value="">Todas</option>
                            @foreach ($filterOptions['purposes'] as $purpose)
                                <option value="{{ $purpose['value'] }}" @selected(($filters['purpose'] ?? '') === $purpose['value'])>
                                    {{ $purpose['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        Quartos
                        <select name="bedrooms">
                            <option value="">Qualquer</option>
                            @foreach ($filterOptions['bedroom_options'] as $bedroomOption)
                                <option value="{{ $bedroomOption }}" @selected((string) ($filters['bedrooms'] ?? '') === (string) $bedroomOption)>
                                    {{ $bedroomOption }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        Banheiros
                        <select name="bathrooms">
                            <option value="">Qualquer</option>
                            @foreach ($filterOptions['bathroom_options'] as $bathroomOption)
                                <option value="{{ $bathroomOption }}" @selected((string) ($filters['bathrooms'] ?? '') === (string) $bathroomOption)>
                                    {{ $bathroomOption }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        Tipo do Imovel
                        <select name="property_type">
                            <option value="">Todos</option>
                            @foreach ($filterOptions['types'] as $type)
                                <option value="{{ $type['value'] }}" @selected(($filters['property_type'] ?? '') === $type['value'])>
                                    {{ $type['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        Faixa de Preco
                        <select name="price_range">
                            <option value="">Todas</option>
                            @foreach ($filterOptions['price_ranges'] as $rangeOption)
                                <option value="{{ $rangeOption['value'] }}" @selected(($filters['price_range'] ?? '') === $rangeOption['value'])>
                                    {{ $rangeOption['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        Preco minimo
                        <input type="number" step="1000" name="min_price" value="{{ $filters['min_price'] ?? '' }}">
                    </label>
                    <label>
                        Preco maximo
                        <input type="number" step="1000" name="max_price" value="{{ $filters['max_price'] ?? '' }}">
                    </label>
                    <button type="submit" class="btn btn-primary w-full">Aplicar filtros</button>
                    <a href="{{ route('properties.index') }}" class="btn btn-ghost w-full">Limpar</a>
                </form>
            </aside>
            <div>
                @php
                    $whatsMessage = rawurlencode('Ola! Vim pela pagina de imoveis e quero atendimento.');
                    $whatsLink = $siteBroker?->whatsapp_url ? $siteBroker->whatsapp_url.'?text='.$whatsMessage : null;
                @endphp
                @if ($siteBroker)
                    <div class="broker-banner">
                        <div class="broker-banner-head">
                            <p class="broker-banner-eyebrow">{{ $siteBroker->broker_display_title }}</p>
                            <strong>{{ $siteBroker->name }}</strong>
                            <small>{{ $siteBroker->broker_bio ?: 'Atendimento rapido para compra e aluguel.' }}</small>
                        </div>
                        <div class="broker-contact-actions">
                            @if ($whatsLink)
                                <a class="broker-contact-link is-whatsapp" href="{{ $whatsLink }}" target="_blank" rel="noopener noreferrer">
                                    Falar no WhatsApp
                                </a>
                            @endif
                            <a class="broker-contact-link" href="mailto:{{ $siteBroker->email }}">
                                Enviar e-mail
                            </a>
                        </div>
                    </div>
                @endif
                <div class="section-head">
                    <h1>Imoveis disponiveis</h1>
                    <small>{{ $properties->total() }} resultados</small>
                </div>
                <div class="property-grid">
                    @forelse ($properties as $property)
                        @include('partials.property-card', ['property' => $property])
                    @empty
                        <p>Nenhum imovel encontrado.</p>
                    @endforelse
                </div>
                <div class="pagination-wrap">
                    {{ $properties->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
