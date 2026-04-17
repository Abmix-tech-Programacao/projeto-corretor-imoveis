@php
    $editing = isset($location);
@endphp

<section class="admin-form-section">
    <div class="admin-form-section-head">
        <h2>Dados da localizacao</h2>
        <p>Crie cidades e subdivisoes para o filtro do site.</p>
    </div>

    <div class="form-grid">
        <label>
            Nome
            <input type="text" name="name" value="{{ old('name', $location->name ?? '') }}" required>
        </label>

        <label>
            Nivel pai (opcional)
            <select name="parent_id">
                <option value="">Sem pai (cidade)</option>
                @foreach ($parentOptions as $option)
                    <option value="{{ $option['id'] }}" @selected((string) old('parent_id', $location->parent_id ?? '') === (string) $option['id'])>
                        {{ $option['label'] }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            Ordem
            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $location->sort_order ?? 0) }}">
        </label>
    </div>

    @if ($editing)
        <p class="inline-hint">Slug atual: <strong>{{ $location->slug }}</strong></p>
    @endif
</section>

<div class="admin-form-actions">
    <button type="submit" class="btn btn-primary">{{ $editing ? 'Salvar alteracoes' : 'Criar localizacao' }}</button>
</div>
