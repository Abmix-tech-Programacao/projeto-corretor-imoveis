<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Painel | Chave na Mao')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    @php
        $adminUser = auth()->user();
        $nameParts = preg_split('/\s+/', trim((string) ($adminUser?->name ?? ''))) ?: [];
        $initials = collect($nameParts)
            ->filter()
            ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->implode('');
        $adminInitials = $initials !== '' ? $initials : 'CN';

        $adminNav = [
            ['route' => 'admin.dashboard', 'pattern' => 'admin.dashboard', 'label' => 'Dashboard', 'hint' => 'Resumo geral'],
            ['route' => 'admin.properties.index', 'pattern' => 'admin.properties.*', 'label' => 'Imoveis', 'hint' => 'Cadastro e edicao'],
            ['route' => 'admin.locations.index', 'pattern' => 'admin.locations.*', 'label' => 'Localizacoes', 'hint' => 'Hierarquia de regioes'],
            ['route' => 'admin.filter-options.index', 'pattern' => 'admin.filter-options.*', 'label' => 'Filtros', 'hint' => 'Opcoes do site'],
            ['route' => 'admin.users.index', 'pattern' => 'admin.users.*', 'label' => 'Usuarios', 'hint' => 'Acesso e corretores'],
            ['route' => 'admin.profile.edit', 'pattern' => 'admin.profile.*', 'label' => 'Meu perfil', 'hint' => 'Dados e senha'],
            ['route' => 'admin.leads.index', 'pattern' => 'admin.leads.*', 'label' => 'Leads', 'hint' => 'Contatos recebidos'],
        ];
    @endphp

    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                @if (filled($adminUser?->photo_path))
                    <img src="{{ $adminUser->photo_path }}" alt="{{ $adminUser->name }}" class="admin-brand-avatar">
                @else
                    <span class="admin-brand-fallback">{{ $adminInitials }}</span>
                @endif
                <div>
                    <strong>Painel Chave na Mao</strong>
                    <small>{{ $adminUser?->name }}</small>
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
