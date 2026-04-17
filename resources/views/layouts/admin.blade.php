<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Painel | Chave na Mão')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    @php
        $adminNav = [
            ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'Dashboard', 'hint' => 'Resumo geral'],
            ['route' => 'admin.properties.index', 'pattern' => 'admin.properties.*', 'label' => 'Imoveis', 'hint' => 'Cadastro e edicao'],
            ['route' => 'admin.locations.index', 'pattern' => 'admin.locations.*', 'label' => 'Localizacoes', 'hint' => 'Hierarquia de regioes'],
            ['route' => 'admin.filter-options.index', 'pattern' => 'admin.filter-options.*', 'label' => 'Filtros', 'hint' => 'Opcoes do site'],
            ['route' => 'admin.leads.index', 'pattern' => 'admin.leads.*', 'label' => 'Leads', 'hint' => 'Contatos recebidos'],
        ];
    @endphp

    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <span>CN</span>
                <div>
                    <strong>Painel Chave na Mão</strong>
                    <small>Gestao simples de imoveis</small>
                </div>
            </a>

            <nav class="admin-nav">
                @foreach ($adminNav as $item)
                    @php
                        $isActive = request()->routeIs($item['pattern']);
                    @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="admin-nav-link {{ $isActive ? 'is-active' : '' }}"
                        @if ($isActive) aria-current="page" @endif
                    >
                        <strong>{{ $item['label'] }}</strong>
                        <small>{{ $item['hint'] }}</small>
                    </a>
                @endforeach

                <a class="admin-nav-link" href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">
                    <strong>Ver site</strong>
                    <small>Abre em nova aba</small>
                </a>
            </nav>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-ghost w-full">Sair do painel</button>
            </form>
        </aside>

        <main class="admin-content">
            @if (session('success'))
                <div class="flash-success admin-flash">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="flash-error admin-flash">{{ $errors->first() }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
