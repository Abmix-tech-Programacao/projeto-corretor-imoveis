@extends('layouts.admin')

@section('title', 'Editar usuario | Painel Chave na Mao')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Editar usuario</h1>
            <p>Atualize permissoes e dados de corretor.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Voltar para lista</a>
    </section>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="admin-card admin-form">
        @csrf
        @method('PUT')
        @include('admin.users._form')
    </form>
@endsection
