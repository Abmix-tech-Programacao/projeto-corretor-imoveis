@extends('layouts.admin')

@section('title', 'Nova localização | Painel Chave na Mão')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Nova localização</h1>
            <p>Adicione uma cidade ou subdivisão para melhorar os filtros.</p>
        </div>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-ghost">Voltar para lista</a>
    </section>

    <form action="{{ route('admin.locations.store') }}" method="POST" class="admin-card admin-form">
        @csrf
        @include('admin.locations._form')
    </form>
@endsection
