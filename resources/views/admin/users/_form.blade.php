@php
    $editing = isset($user);
@endphp

<section class="admin-form-section">
    <div class="admin-form-section-head">
        <h2>Dados de acesso</h2>
        <p>Configure login e permissao de painel.</p>
    </div>

    <div class="form-grid">
        <label>
            Nome
            <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required>
        </label>
        <label>
            E-mail
            <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
        </label>
        <label>
            Senha {{ $editing ? '(preencha apenas se quiser alterar)' : '' }}
            <input type="password" name="password" {{ $editing ? '' : 'required' }}>
        </label>
        <label>
            Confirmar senha
            <input type="password" name="password_confirmation" {{ $editing ? '' : 'required' }}>
        </label>
        <label class="checkbox-line">
            <input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user->is_admin ?? false))>
            Usuario administrador
        </label>
    </div>
</section>

<section class="admin-form-section">
    <div class="admin-form-section-head">
        <h2>Perfil do corretor</h2>
        <p>Esses dados podem aparecer no site publico.</p>
    </div>

    <div class="form-grid">
        <label>
            Titulo profissional
            <input type="text" name="broker_title" value="{{ old('broker_title', $user->broker_title ?? 'Corretor especialista') }}">
        </label>
        <label>
            CRECI
            <input type="text" name="creci" value="{{ old('creci', $user->creci ?? '') }}">
        </label>
        <label>
            Telefone
            <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
        </label>
        <label>
            WhatsApp
            <input type="text" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp ?? '') }}">
        </label>
    </div>

    <label>
        Bio curta
        <textarea name="broker_bio" rows="3">{{ old('broker_bio', $user->broker_bio ?? '') }}</textarea>
    </label>

    <div class="form-grid">
        <label>
            Foto do corretor
            <input type="file" name="photo_upload" accept="image/*">
        </label>
        @if ($editing && filled($user->photo_path))
            <label class="checkbox-line">
                <input type="checkbox" name="remove_photo" value="1">
                Remover foto atual
            </label>
        @endif
    </div>

    @if ($editing && filled($user->photo_path))
        <div class="admin-card admin-card-soft">
            <h3>Foto atual</h3>
            <img src="{{ $user->photo_path }}" alt="{{ $user->name }}" class="admin-avatar-preview">
        </div>
    @endif
</section>

<div class="admin-form-actions">
    <button type="submit" class="btn btn-primary">{{ $editing ? 'Salvar alteracoes' : 'Criar usuario' }}</button>
</div>
