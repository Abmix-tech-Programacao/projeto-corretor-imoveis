@extends('layouts.admin')

@section('title', 'Novo imovel | Painel Chave na Mão')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Novo imovel</h1>
            <p>Preencha os campos abaixo para cadastrar um novo anuncio.</p>
        </div>
        <a href="{{ route('admin.properties.index') }}" class="btn btn-ghost">Voltar para lista</a>
    </section>

    <form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data" class="admin-card admin-form">
        @csrf
        @include('admin.properties._form')
    </form>
@endsection
