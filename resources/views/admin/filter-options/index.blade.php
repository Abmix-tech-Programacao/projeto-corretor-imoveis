@extends('layouts.admin')

@section('title', 'Filtros | Painel Chave na Mão')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Filtros</h1>
            <p>Gerencie opções exibidas no site e no menu.</p>
        </div>
        <a href="{{ route('admin.filter-options.create') }}" class="btn btn-primary">Nova opção</a>
    </section>

    <div class="admin-card">
        <form action="{{ route('admin.filter-options.index') }}" method="GET" class="filter-inline-form">
            <label>
                Grupo
                <select name="group" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    @foreach ($groups as $groupKey => $groupLabel)
                        <option value="{{ $groupKey }}" @selected($selectedGroup === $groupKey)>{{ $groupLabel }}</option>
                    @endforeach
                </select>
            </label>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Grupo</th>
                        <th>Label</th>
                        <th>Valor</th>
                        <th>Ordem</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($options as $option)
                        <tr>
                            <td>{{ $groups[$option->group_key] ?? $option->group_key }}</td>
                            <td>{{ $option->label }}</td>
                            <td>{{ $option->value }}</td>
                            <td>{{ $option->sort_order }}</td>
                            <td>
                                <span class="status-chip {{ $option->is_active ? 'is-success' : 'is-muted' }}">
                                    {{ $option->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="actions">
                                <a class="btn btn-ghost" href="{{ route('admin.filter-options.edit', $option) }}">Editar</a>
                                <form action="{{ route('admin.filter-options.destroy', $option) }}" method="POST" onsubmit="return confirm('Excluir esta opção?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Nenhuma opção cadastrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $options->links() }}
        </div>
    </div>
@endsection
