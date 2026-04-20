<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Chave na Mão - imóveis em São Paulo e região metropolitana.">
    <title>@yield('title', 'Chave na Mão')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @php
        $menuCategories = \App\Support\FilterCatalog::menuCategories();
        if ($menuCategories->isEmpty()) {
            $menuCategories = collect([
                ['value' => 'lancamento', 'label' => 'Lançamentos'],
                ['value' => 'breve-lancamento', 'label' => 'Breve Lançamento'],
                ['value' => 'imovel-pronto', 'label' => 'Imóvel Pronto'],
                ['value' => 'para-alugar', 'label' => 'Para Alugar'],
            ]);
        }

        $activeMenuCategory = (string) request('menu_category', '');
        $footerBroker = $siteBroker ?? null;
        $footerPhoneDigits = $footerBroker?->phone ? preg_replace('/\D+/', '', $footerBroker->phone) : null;
    @endphp

    <header class="topbar">
        <div class="container topbar-row">
            <div class="brand">
                <a href="{{ route('home') }}">
                    <img src="/images/logo.png" alt="Logo" class="brand-logo-img">
                </a>
            </div>
            <button class="menu-toggle" type="button" data-menu-toggle aria-label="Abrir menu">Menu</button>
            <nav class="main-nav" data-main-nav>
                <a href="{{ route('home') }}" class="menu-link {{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a>
                @foreach ($menuCategories as $menuCategory)
                    <a
                        href="{{ route('properties.index', ['menu_category' => $menuCategory['value']]) }}"
                        class="menu-link {{ request()->routeIs('properties.index') && $activeMenuCategory === $menuCategory['value'] ? 'is-active' : '' }}"
                    >
                        {{ $menuCategory['label'] }}
                    </a>
                @endforeach
                <a class="btn btn-primary" href="{{ route('admin.login') }}">Área admin</a>
            </nav>
        </div>
    </header>

    @if (session('success'))
        <div class="flash-success">
            <div class="container">{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="flash-error">
            <div class="container">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer id="contato" class="site-footer">
        <div class="container footer-grid">
            <div>
                <h4>Chave na Mão</h4>
                <p>Especialista em apartamentos novos e lançamentos em São Paulo.</p>
            </div>
            <div>
                <h5>Contato</h5>
                <ul>
                    @if ($footerBroker?->phone)
                        <li>
                            <a href="tel:{{ $footerPhoneDigits }}">{{ $footerBroker->phone }}</a>
                        </li>
                    @endif
                    @if ($footerBroker?->email)
                        <li><a href="mailto:{{ $footerBroker->email }}">{{ $footerBroker->email }}</a></li>
                    @endif
                    <li>Seg a Sáb: 08h às 20h</li>
                </ul>
            </div>
            <div>
                <h5>Links</h5>
                <ul>
                    <li><a href="{{ route('home') }}">Página inicial</a></li>
                    <li><a href="{{ route('properties.index') }}">Todos os imóveis</a></li>
                    <li><a href="{{ route('admin.login') }}">Painel admin</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>
