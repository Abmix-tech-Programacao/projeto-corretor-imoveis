<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesBrokerPhoto;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    use HandlesBrokerPhoto;

    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['photo_upload']);
        $data['is_admin'] = $request->boolean('is_admin');

        if ($request->hasFile('photo_upload')) {
            $data['photo_path'] = $this->storeBrokerPhoto($request->file('photo_upload'));
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        unset($data['photo_upload'], $data['remove_photo']);
        $isAdmin = $request->boolean('is_admin');

        if ($user->id === (int) auth()->id() && ! $isAdmin) {
            return back()->withErrors(['is_admin' => 'Seu usuário precisa permanecer como administrador.'])->withInput();
        }

        if ($user->is_admin && ! $isAdmin && User::query()->where('is_admin', true)->count() <= 1) {
            return back()->withErrors(['is_admin' => 'É preciso manter ao menos um administrador no sistema.'])->withInput();
        }

        $data['is_admin'] = $isAdmin;

        if ($request->hasFile('photo_upload')) {
            $this->deleteBrokerPhotoIfInternal($user->photo_path);
            $data['photo_path'] = $this->storeBrokerPhoto($request->file('photo_upload'));
        } elseif ($request->boolean('remove_photo')) {
            $this->deleteBrokerPhotoIfInternal($user->photo_path);
            $data['photo_path'] = null;
        }

        if (! filled($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.edit', $user)->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === (int) auth()->id()) {
            return back()->withErrors(['user' => 'Você não pode excluir o próprio usuário.']);
        }

        if ($user->is_admin && User::query()->where('is_admin', true)->count() <= 1) {
            return back()->withErrors(['user' => 'Não é possível excluir o último administrador.']);
        }

        $this->deleteBrokerPhotoIfInternal($user->photo_path);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuário removido.');
    }
}
