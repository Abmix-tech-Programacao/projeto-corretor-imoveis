@extends('layouts.admin')

@section('title', 'Editar localização | Painel Chave na Mão')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Editar localização</h1>
            <p>Revise nome, hierarquia e ordem de exibicao.</p>
        </div>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-ghost">Voltar para lista</a>
    </section>

    <form action="{{ route('admin.locations.update', $location) }}" method="POST" class="admin-card admin-form">
        @csrf
        @method('PUT')
        @include('admin.locations._form', ['location' => $location])
    </form>
@endsection
