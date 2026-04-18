@extends('layouts.admin')

@section('title', 'Nova opção de filtro | Painel Chave na Mão')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Nova opção de filtro</h1>
            <p>Cadastre uma nova opção para busca e navegação do site.</p>
        </div>
        <a href="{{ route('admin.filter-options.index') }}" class="btn btn-ghost">Voltar para lista</a>
    </section>

    <form action="{{ route('admin.filter-options.store') }}" method="POST" class="admin-card admin-form">
        @csrf
        @include('admin.filter-options._form')
    </form>
@endsection
