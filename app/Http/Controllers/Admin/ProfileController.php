<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesBrokerPhoto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use HandlesBrokerPhoto;

    public function edit(): View
    {
        return view('admin.profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        unset($data['photo_upload'], $data['remove_photo']);

        if ($request->hasFile('photo_upload')) {
            $this->deleteBrokerPhotoIfInternal($user->photo_path);
            $data['photo_path'] = $this->storeBrokerPhoto($request->file('photo_upload'));
        } elseif ($request->boolean('remove_photo')) {
            $this->deleteBrokerPhotoIfInternal($user->photo_path);
            $data['photo_path'] = null;
        }

        $user->update($data);

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('new_password'),
        ]);

        return back()->with('success', 'Senha atualizada com sucesso.');
    }
}
