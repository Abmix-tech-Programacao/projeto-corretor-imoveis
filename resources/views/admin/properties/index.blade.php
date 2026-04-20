@extends('layouts.admin')

@section('title', 'Imoveis | Painel Chave na Mao')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Imoveis</h1>
            <p>{{ $properties->total() }} cadastro(s) encontrado(s). Edite ou publique em poucos cliques.</p>
        </div>
        <a href="{{ route('admin.properties.create') }}" class="btn btn-primary">Novo imovel</a>
    </section>

    <div class="admin-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titulo</th>
                        <th>Menu</th>
                        <th>Corretor</th>
                        <th>Localizacao</th>
                        <th>Cidade/Bairro</th>
                        <th>Preco</th>
                        <th>Status</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($properties as $property)
                        <tr>
                            <td>#{{ $property->id }}</td>
                            <td>{{ $property->title }}</td>
                            <td>
                                <span class="status-chip is-muted">{{ $property->menu_category ?: 'Sem menu' }}</span>
                            </td>
                            <td>{{ $property->broker?->name ?? '-' }}</td>
                            <td>{{ $property->location?->name ?? '-' }}</td>
                            <td>{{ $property->neighborhood }} - {{ $property->city }}/{{ $property->state }}</td>
                            <td>{{ $property->formatted_price }}</td>
                            <td>
                                <span class="status-chip {{ $property->is_published ? 'is-success' : 'is-muted' }}">
                                    {{ $property->is_published ? 'Publicado' : 'Rascunho' }}
                                </span>
                            </td>
                            <td class="actions">
                                <a class="btn btn-ghost" href="{{ route('admin.properties.edit', $property) }}">Editar</a>
                                <form action="{{ route('admin.properties.destroy', $property) }}" method="POST" onsubmit="return confirm('Remover este imovel?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9">Nenhum imovel cadastrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $properties->links() }}
        </div>
    </div>
@endsection
