@extends('layouts.admin')

@section('title', 'Editar opcao de filtro | Painel Chave na Mão')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Editar opcao de filtro</h1>
            <p>Altere label, valor interno e ordem de exibicao.</p>
        </div>
        <a href="{{ route('admin.filter-options.index') }}" class="btn btn-ghost">Voltar para lista</a>
    </section>

    <form action="{{ route('admin.filter-options.update', $option) }}" method="POST" class="admin-card admin-form">
        @csrf
        @method('PUT')
        @include('admin.filter-options._form', ['option' => $option])
    </form>
@endsection
