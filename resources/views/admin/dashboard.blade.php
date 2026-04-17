@extends('layouts.admin')

@section('title', 'Dashboard | Painel Chave na Mão')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Dashboard</h1>
            <p>Visao geral rapida do que entrou no sistema.</p>
        </div>
        <a href="{{ route('admin.properties.create') }}" class="btn btn-primary">Novo imovel</a>
    </section>

    <section class="metric-grid">
        <article>
            <small>Total de imoveis</small>
            <strong>{{ $propertyCount }}</strong>
        </article>
        <article>
            <small>Imoveis publicados</small>
            <strong>{{ $publishedCount }}</strong>
        </article>
        <article>
            <small>Leads recebidos</small>
            <strong>{{ $leadCount }}</strong>
        </article>
    </section>

    <section class="admin-card-grid">
        <article class="admin-card">
            <div class="admin-card-head">
                <h2>Leads recentes</h2>
                <small>Ultimos contatos recebidos no site</small>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Imovel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentLeads as $lead)
                            <tr>
                                <td>{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $lead->name }}</td>
                                <td>{{ $lead->phone }}</td>
                                <td>{{ $lead->property?->title ?? 'Contato geral' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4">Sem leads ainda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <article class="admin-card">
            <div class="admin-card-head">
                <h2>Imoveis recentes</h2>
                <small>Ultimos cadastrados no painel</small>
            </div>
            <ul class="simple-list">
                @forelse ($recentProperties as $property)
                    <li>
                        <strong>{{ $property->title }}</strong>
                        <small>{{ $property->city }}/{{ $property->state }} - {{ $property->formatted_price }}</small>
                    </li>
                @empty
                    <li>
                        <strong>Nenhum imovel cadastrado.</strong>
                        <small>Use o botao "Novo imovel" para comecar.</small>
                    </li>
                @endforelse
            </ul>
        </article>
    </section>
@endsection
