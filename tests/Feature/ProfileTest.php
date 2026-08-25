<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk()
            ->assertSeeText('Edit profil')
            ->assertSeeText('Foto KTP')
            ->assertSeeText('Ubah password')
            ->assertSee("window.addEventListener('pageshow', hideFullScreenLoader)", false);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_manage_and_rotate_their_own_profile_photos(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['username' => 'profil.sendiri']);

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Profil Sendiri',
            'username' => 'profil.sendiri',
            'email' => $user->email,
            'no_telf' => '081234567890',
            'profile_photo' => UploadedFile::fake()->image('profil.jpg', 180, 240),
            'ktp_photo' => UploadedFile::fake()->image('ktp.jpg', 320, 200),
            'ktp_rotation' => 90,
            'profile_crop_y' => 70,
            'profile_zoom' => 1.4,
            'ktp_crop_x' => 35,
            'ktp_crop_y' => 60,
            'ktp_zoom' => 1.3,
        ])->assertSessionHasNoErrors()->assertRedirect(route('profile.edit'));

        $user->refresh();
        Storage::disk('local')->assertExists($user->profile_photo_path);
        Storage::disk('local')->assertExists($user->ktp_photo_path);
        Storage::disk('local')->assertExists(dirname($user->profile_photo_path).'/originals/'.basename($user->profile_photo_path));
        Storage::disk('local')->assertExists(dirname($user->ktp_photo_path).'/originals/'.basename($user->ktp_photo_path));
        $this->assertSame([900, 900], array_slice(getimagesize(Storage::disk('local')->path($user->profile_photo_path)), 0, 2));
        $this->assertSame([1284, 810], array_slice(getimagesize(Storage::disk('local')->path($user->ktp_photo_path)), 0, 2));

        $this->get(route('users.photo', [$user, 'profile']))->assertOk();
        $this->get(route('users.photo', [$user, 'ktp']))->assertOk();
        $this->get(route('profile.edit'))
            ->assertOk()
            ->assertSeeText('85,6 × 54 mm')
            ->assertSeeText('Geser vertikal')
            ->assertSee('name="profile_zoom"', false)
            ->assertSee('name="ktp_zoom"', false)
            ->assertSee('userImageEditor(', false);

        $this->patch(route('profile.update'), [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'no_telf' => $user->no_telf,
            'ktp_rotation' => 90,
            'ktp_crop_x' => 50,
            'ktp_crop_y' => 50,
            'ktp_zoom' => 1.8,
            'ktp_transform_changed' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertSame([1284, 810], array_slice(getimagesize(Storage::disk('local')->path($user->ktp_photo_path)), 0, 2));
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
