<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\PrivateImageStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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
        $imageChanged = false;
        if (isset($data['username'])) {
            $data['username'] = strtolower($data['username']);
        }

        try {
            foreach ([['profile_photo', 'profile_photo_path', 'remove_profile_photo'], ['ktp_photo', 'ktp_photo_path', 'remove_ktp_photo']] as [$input, $column, $remove]) {
                if ($request->hasFile($input)) {
                    $imageChanged = true;
                    $rotation = $input === 'ktp_photo' ? (int) ($data['ktp_rotation'] ?? 0) : 0;
                    $directory = $input === 'ktp_photo' ? 'users/ktp' : 'users/profile';
                    $kind = $input === 'ktp_photo' ? 'ktp' : 'profile';
                    $newPath = $images->store(
                        $request->file($input),
                        $directory,
                        $rotation,
                        $this->cropOptions($data, $kind),
                    )['path'];
                    if ($user->{$column}) {
                        $images->deleteStored($user->{$column});
                    }
                    $data[$column] = $newPath;
                } elseif ($request->boolean($remove)) {
                    $imageChanged = true;
                    $images->deleteStored($user->{$column});
                    $data[$column] = null;
                } elseif ($user->{$column}) {
                    if ($input === 'ktp_photo' && (int) ($data['ktp_rotation'] ?? 0) !== 0) {
                        $imageChanged = true;
                        if (! $images->rotateStored($user->ktp_photo_path, (int) $data['ktp_rotation'])) {
                            throw new \RuntimeException('KTP tidak dapat diputar.');
                        }
                    }
                    $kind = $input === 'ktp_photo' ? 'ktp' : 'profile';
                    if ($request->boolean("{$kind}_transform_changed") && ! $images->cropStored($user->{$column}, $this->cropOptions($data, $kind))) {
                        throw new \RuntimeException('Posisi foto tidak dapat disimpan.');
                    }
                    $imageChanged = $imageChanged || $request->boolean("{$kind}_transform_changed");
                }
                unset($data[$input], $data[$remove]);
            }
        } catch (\RuntimeException) {
            throw ValidationException::withMessages(['ktp_photo' => 'Foto tidak dapat diproses. Coba unggah ulang dengan format JPG, PNG, atau WebP.']);
        }
        $this->forgetImageInputs($data);

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
        if ($imageChanged) {
            $user->touch();
        }

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

    private function cropOptions(array $data, string $kind): array
    {
        return $kind === 'profile'
            ? [
                'aspect_width' => 1,
                'aspect_height' => 1,
                'output_width' => 900,
                'output_height' => 900,
                'x' => (float) ($data['profile_crop_x'] ?? 50),
                'y' => (float) ($data['profile_crop_y'] ?? 50),
                'zoom' => (float) ($data['profile_zoom'] ?? 1),
            ]
            : [
                'aspect_width' => 856,
                'aspect_height' => 540,
                'output_width' => 1284,
                'output_height' => 810,
                'x' => (float) ($data['ktp_crop_x'] ?? 50),
                'y' => (float) ($data['ktp_crop_y'] ?? 50),
                'zoom' => (float) ($data['ktp_zoom'] ?? 1),
            ];
    }

    private function forgetImageInputs(array &$data): void
    {
        foreach ([
            'profile_photo', 'ktp_photo', 'ktp_rotation',
            'profile_crop_x', 'profile_crop_y', 'profile_zoom', 'profile_transform_changed',
            'ktp_crop_x', 'ktp_crop_y', 'ktp_zoom', 'ktp_transform_changed',
        ] as $key) {
            unset($data[$key]);
        }
    }
}
