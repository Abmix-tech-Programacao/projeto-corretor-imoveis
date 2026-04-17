@php
    $editing = isset($option);
@endphp

<section class="admin-form-section">
    <div class="admin-form-section-head">
        <h2>Dados da opcao</h2>
        <p>Use labels claros para o cliente e valores estaveis para o sistema.</p>
    </div>

    <div class="form-grid">
        <label>
            Grupo
            <select name="group_key" required>
                @foreach ($groups as $groupKey => $groupLabel)
                    <option value="{{ $groupKey }}" @selected(old('group_key', $option->group_key ?? '') === $groupKey)>{{ $groupLabel }}</option>
                @endforeach
            </select>
        </label>

        <label>
            Label (texto exibido)
            <input type="text" name="label" value="{{ old('label', $option->label ?? '') }}" required>
        </label>

        <label>
            Valor (interno)
            <input type="text" name="value" value="{{ old('value', $option->value ?? '') }}" placeholder="Ex.: venda, 3, 200000|350000">
        </label>

        <label>
            Ordem
            <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $option->sort_order ?? 0) }}">
        </label>
    </div>

    <label class="checkbox-line">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $option->is_active ?? true))>
        Opcao ativa
    </label>

    @if ($editing)
        <p class="inline-hint">ID: {{ $option->id }}</p>
    @endif
    <p class="inline-hint">Para Faixa de Preco use formato min|max. Exemplo: 200000|350000 ou 500001|</p>
</section>

<div class="admin-form-actions">
    <button type="submit" class="btn btn-primary">{{ $editing ? 'Salvar alteracoes' : 'Criar opcao' }}</button>
</div>
