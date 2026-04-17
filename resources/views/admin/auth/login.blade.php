<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin | Chave na Mão</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-login-body">
    <main class="admin-login-card">
        <h1>Acesso administrativo</h1>
        <p>Entre para gerenciar os imóveis e leads.</p>

        @if ($errors->any())
            <div class="flash-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf
            <label>
                E-mail
                <input type="email" name="email" value="{{ old('email') }}" required>
            </label>
            <label>
                Senha
                <input type="password" name="password" required>
            </label>
            <label class="checkbox-line">
                <input type="checkbox" name="remember" value="1">
                Manter conectado
            </label>
            <button type="submit" class="btn btn-primary w-full">Entrar</button>
        </form>
        <a href="{{ route('home') }}">Voltar para o site</a>
    </main>
</body>
</html>
