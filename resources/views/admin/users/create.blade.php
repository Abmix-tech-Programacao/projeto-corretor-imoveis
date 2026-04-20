@extends('layouts.admin')

@section('title', 'Novo usuario | Painel Chave na Mao')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Novo usuario</h1>
            <p>Cadastre contas para administradores e corretores.</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Voltar para lista</a>
    </section>

    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="admin-card admin-form">
        @csrf
        @include('admin.users._form')
    </form>
@endsection
