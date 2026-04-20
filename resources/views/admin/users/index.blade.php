@extends('layouts.admin')

@section('title', 'Usuarios | Painel Chave na Mao')

@section('content')
    <section class="admin-header">
        <div>
            <h1>Usuarios</h1>
            <p>{{ $users->total() }} usuario(s) cadastrados.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Novo usuario</a>
    </section>

    <div class="admin-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th>Telefone</th>
                        <th>WhatsApp</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                @if ($user->photo_path)
                                    <img src="{{ $user->photo_path }}" alt="{{ $user->name }}" class="admin-avatar-thumb">
                                @else
                                    <span class="admin-avatar-fallback">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @endif
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="status-chip {{ $user->is_admin ? 'is-success' : 'is-muted' }}">
                                    {{ $user->is_admin ? 'Administrador' : 'Corretor' }}
                                </span>
                            </td>
                            <td>{{ $user->phone ?: '-' }}</td>
                            <td>{{ $user->whatsapp ?: '-' }}</td>
                            <td class="actions">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost">Editar</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Remover este usuario?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">Nenhum usuario cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $users->links() }}
        </div>
    </div>
@endsection
