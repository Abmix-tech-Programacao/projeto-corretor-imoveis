@extends('layouts.admin')

@section('title', 'Leads | Painel Chave na Mão')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Leads</h1>
            <p>{{ $leads->total() }} contato(s) recebidos.</p>
        </div>
    </section>

    <div class="admin-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Imovel</th>
                        <th>Mensagem</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leads as $lead)
                        <tr>
                            <td>{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $lead->name }}</td>
                            <td><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></td>
                            <td>{{ $lead->phone }}</td>
                            <td>{{ $lead->property?->title ?? 'Contato geral' }}</td>
                            <td>{{ $lead->message ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Sem leads cadastrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $leads->links() }}
        </div>
    </div>
@endsection
