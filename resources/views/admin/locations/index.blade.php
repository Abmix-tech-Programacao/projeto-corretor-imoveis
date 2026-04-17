@extends('layouts.admin')

@section('title', 'Localizacoes | Painel Chave na Mão')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Localizacoes</h1>
            <p>Organize regioes para facilitar filtros no site.</p>
        </div>
        <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">Nova localizacao</a>
    </section>

    <div class="admin-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Slug</th>
                        <th>Nivel</th>
                        <th>Pai</th>
                        <th>Subitens</th>
                        <th>Imoveis</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr>
                            <td>{{ str_repeat('- ', $row['depth']) }}{{ $row['name'] }}</td>
                            <td>{{ $row['slug'] }}</td>
                            <td>{{ $row['depth'] + 1 }}</td>
                            <td>{{ $row['parent_name'] ?: '-' }}</td>
                            <td>{{ $row['children_count'] }}</td>
                            <td>{{ $row['properties_count'] }}</td>
                            <td class="actions">
                                <a class="btn btn-ghost" href="{{ route('admin.locations.edit', $row['id']) }}">Editar</a>
                                <form action="{{ route('admin.locations.destroy', $row['id']) }}" method="POST" onsubmit="return confirm('Excluir esta localizacao?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">Nenhuma localizacao cadastrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
