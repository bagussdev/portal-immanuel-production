<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\PrivateImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request, PrivateImageStorage $images): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        if (isset($data['username'])) {
            $data['username'] = strtolower($data['username']);
        }

        try {
            foreach ([['profile_photo', 'profile_photo_path', 'remove_profile_photo'], ['ktp_photo', 'ktp_photo_path', 'remove_ktp_photo']] as [$input, $column, $remove]) {
                if ($request->hasFile($input)) {
                    $rotation = $input === 'ktp_photo' ? (int) ($data['ktp_rotation'] ?? 0) : 0;
                    $directory = $input === 'ktp_photo' ? 'users/ktp' : 'users/profile';
                    $newPath = $images->store($request->file($input), $directory, $rotation)['path'];
                    if ($user->{$column}) {
                        Storage::disk('local')->delete($user->{$column});
                    }
                    $data[$column] = $newPath;
                } elseif ($request->boolean($remove)) {
                    if ($user->{$column}) {
                        Storage::disk('local')->delete($user->{$column});
                    }
                    $data[$column] = null;
                } elseif ($input === 'ktp_photo' && (int) ($data['ktp_rotation'] ?? 0) !== 0 && $user->ktp_photo_path) {
                    if (! $images->rotateStored($user->ktp_photo_path, (int) $data['ktp_rotation'])) {
                        throw new \RuntimeException('KTP tidak dapat diputar.');
                    }
                }
                unset($data[$input], $data[$remove]);
            }
        } catch (\RuntimeException) {
            throw ValidationException::withMessages(['ktp_photo' => 'Foto tidak dapat diproses. Coba unggah ulang dengan format JPG, PNG, atau WebP.']);
        }
        unset($data['ktp_rotation']);

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
