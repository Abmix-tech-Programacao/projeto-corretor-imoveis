@extends('layouts.admin')

@section('title', 'Meu perfil | Painel Chave na Mao')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Meu perfil</h1>
            <p>Atualize seus dados de corretor e senha de acesso.</p>
        </div>
    </section>

    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="admin-card admin-form">
        @csrf
        @method('PUT')

        <section class="admin-form-section">
            <div class="admin-form-section-head">
                <h2>Dados pessoais</h2>
                <p>Essas informacoes aparecem no site quando voce for o corretor responsavel.</p>
            </div>

            <div class="form-grid">
                <label>
                    Nome
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                </label>
                <label>
                    E-mail
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </label>
                <label>
                    Titulo profissional
                    <input type="text" name="broker_title" value="{{ old('broker_title', $user->broker_title ?? 'Corretor especialista') }}">
                </label>
                <label>
                    CRECI
                    <input type="text" name="creci" value="{{ old('creci', $user->creci) }}">
                </label>
                <label>
                    Telefone
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
                </label>
                <label>
                    WhatsApp
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}">
                </label>
            </div>

            <label>
                Bio curta
                <textarea name="broker_bio" rows="3">{{ old('broker_bio', $user->broker_bio) }}</textarea>
            </label>

            <div class="form-grid">
                <label>
                    Foto do corretor
                    <input type="file" name="photo_upload" accept="image/*">
                </label>
                @if (filled($user->photo_path))
                    <label class="checkbox-line">
                        <input type="checkbox" name="remove_photo" value="1">
                        Remover foto atual
                    </label>
                @endif
            </div>

            @if (filled($user->photo_path))
                <div class="admin-card admin-card-soft">
                    <h3>Foto atual</h3>
                    <img src="{{ $user->photo_path }}" alt="{{ $user->name }}" class="admin-avatar-preview">
                </div>
            @endif
        </section>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-primary">Salvar perfil</button>
        </div>
    </form>

    <form action="{{ route('admin.profile.password.update') }}" method="POST" class="admin-card admin-form">
        @csrf
        @method('PUT')

        <section class="admin-form-section">
            <div class="admin-form-section-head">
                <h2>Trocar senha</h2>
                <p>Use no minimo 8 caracteres para a nova senha.</p>
            </div>

            <div class="form-grid">
                <label>
                    Senha atual
                    <input type="password" name="current_password" required>
                </label>
                <label>
                    Nova senha
                    <input type="password" name="new_password" required>
                </label>
                <label>
                    Confirmar nova senha
                    <input type="password" name="new_password_confirmation" required>
                </label>
            </div>
        </section>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-primary">Atualizar senha</button>
        </div>
    </form>
@endsection
