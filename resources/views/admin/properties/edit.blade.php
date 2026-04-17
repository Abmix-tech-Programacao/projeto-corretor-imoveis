@extends('layouts.admin')

@section('title', 'Editar imovel | Painel Chave na Mão')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Editar imovel</h1>
            <p>Atualize as informacoes e salve para refletir no site.</p>
        </div>
        <a href="{{ route('admin.properties.index') }}" class="btn btn-ghost">Voltar para lista</a>
    </section>

    <form action="{{ route('admin.properties.update', $property) }}" method="POST" enctype="multipart/form-data" class="admin-card admin-form">
        @csrf
        @method('PUT')
        @include('admin.properties._form', ['property' => $property])
    </form>
@endsection
