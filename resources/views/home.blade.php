@extends('layouts.app')

@section('title', 'Chave na Mão | Imóveis em São Paulo')

@section('content')
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">LANÇAMENTOS E PRONTOS PARA MORAR</p>
                <h1>Studios e Apartamentos em São Paulo</h1>
                <p>Encontre imóveis selecionados por um corretor com atendimento rápido, tour virtual e simulação de financiamento.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ route('properties.index') }}">Ver imóveis</a>
                    <a class="btn btn-ghost-light" href="#contato">Falar com especialista</a>
                </div>
            </div>
            <form class="search-card" action="{{ route('properties.index') }}" method="GET">
                <h2>Buscar imóvel</h2>
                @include('partials.location-selects')
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
                    Tipo do Imóvel
                    <select name="property_type">
                        <option value="">Todos</option>
                        @foreach ($filterOptions['types'] as $type)
                            <option value="{{ $type['value'] }}" @selected(($filters['property_type'] ?? '') === $type['value'])>
                                {{ $type['label'] }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="btn btn-primary w-full">Pesquisar</button>
            </form>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-head">
                <h2>Nossos imóveis exclusivos</h2>
                <a href="{{ route('properties.index') }}">Ver todos</a>
            </div>
            <div class="property-grid">
                @forelse ($highlightedProperties as $property)
                    @include('partials.property-card', ['property' => $property])
                @empty
                    <p>Nenhum imóvel encontrado para os filtros selecionados.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section section-alt">
        <div class="container">
            <div class="section-head">
                <h2>Últimos lançamentos</h2>
            </div>
            <div class="property-grid compact">
                @foreach ($latestProperties as $property)
                    @include('partials.property-card', ['property' => $property])
                @endforeach
            </div>
        </div>
    </section>
@endsection
