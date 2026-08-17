<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\FieldJob;
use App\Models\FieldJobStage;
use App\Models\Invoice;
use App\Models\NotificationPreference;
use App\Models\Permission;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FieldJobFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_invoice_level_work_flow_applies_to_every_item_and_stage(): void
    {
        $admin = $this->user('admin');
        $invoice = $this->mixedDraft($admin);

        $this->actingAs($admin)->post(route('invoices.issue', $invoice), [
            'issue_date' => today()->toDateString(),
            'due_date' => today()->addDays(14)->toDateString(),
        ])->assertRedirect();

        $job = FieldJob::with(['items', 'stages'])->where('invoice_id', $invoice->id)->firstOrFail();
        $this->assertMatchesRegularExpression('#^JOB/\d{2}/\d{2}/\d{3}$#', $job->job_number);
        $this->assertSame('Instruksi aman untuk tim.', $job->notes);
        $this->assertSame([
            FieldJobStage::TYPE_INSTALL,
            FieldJobStage::TYPE_TEARDOWN,
        ], $job->stages->pluck('type')->sort()->values()->all());
        $this->assertSame(3, $job->items->count());
        $this->assertSame([Invoice::FLOW_INSTALL_TEARDOWN], $job->items->pluck('work_flow')->unique()->values()->all());
        $this->assertArrayNotHasKey('unit_price', $job->items->first()->getAttributes());
        $this->assertArrayNotHasKey('total', $job->items->first()->getAttributes());

        $mandor = $this->user('mandor');
        $this->actingAs($mandor)->get(route('field-jobs.show', $job))
            ->assertOk()
            ->assertSee('Panggung')
            ->assertSee('Lighting')
            ->assertDontSee('Catatan internal: pembayaran Rp 9.999.999')
            ->assertDontSee('9.999.999');
        $this->get(route('invoices.show', $invoice))->assertForbidden();
    }

    public function test_admin_assigns_a_team_and_assigned_user_can_upload_multiple_photos_and_complete_the_job(): void
    {
        Storage::fake('local');
        $admin = $this->user('admin');
        $user = $this->user('user');
        $invoice = $this->mixedDraft($admin);
        $this->actingAs($admin)->post(route('invoices.issue', $invoice), [
            'issue_date' => today()->toDateString(),
        ])->assertRedirect();

        $job = FieldJob::where('invoice_id', $invoice->id)->firstOrFail();
        $install = $job->stages()->where('type', FieldJobStage::TYPE_INSTALL)->firstOrFail();
        $teardown = $job->stages()->where('type', FieldJobStage::TYPE_TEARDOWN)->firstOrFail();

        $this->put(route('field-jobs.stages.assignments', [$job, $install]), [
            'assignee_ids' => [$user->id],
            'copy_to_teardown' => 1,
        ])->assertRedirect();
        $this->assertTrue($install->fresh()->assignees->contains('id', $user->id));
        $this->assertTrue($teardown->fresh()->assignees->contains('id', $user->id));

        $this->actingAs($user)->get(route('field-jobs.index'))->assertOk()->assertSee($job->job_number);
        $this->get(route('field-jobs.show', $job))->assertOk()->assertSee('Instruksi aman untuk tim.');
        $photos = collect(range(1, 9))
            ->map(fn (int $number) => UploadedFile::fake()->image("hasil-pasang-{$number}.jpg", 320, 240))
            ->all();
        $this->post(route('field-jobs.stages.photos.store', [$job, $install]), [
            'photos' => $photos,
            'caption' => 'Hasil pasang sisi depan',
        ])->assertRedirect();

        $install->refresh();
        $this->assertSame(FieldJobStage::STATUS_IN_PROGRESS, $install->status);
        $this->assertCount(9, $install->photos);
        $photo = $install->photos()->firstOrFail();
        Storage::disk('local')->assertExists($photo->path);
        $jobPage = $this->get(route('field-jobs.show', $job))->assertOk();
        foreach ($install->photos as $uploadedPhoto) {
            $jobPage->assertSee(
                route('field-jobs.stages.photos.show', [$job, $install, $uploadedPhoto]),
                false,
            );
        }
        $jobPage->assertSee('data-lightbox-photo', false)
            ->assertSee('x-teleport="body"', false)
            ->assertDontSee('target="_blank"', false);

        $this->patch(route('field-jobs.stages.update', [$job, $install]), ['status' => 'completed'])->assertRedirect();
        $this->assertSame(FieldJobStage::STATUS_COMPLETED, $install->fresh()->status);
        $this->get(route('field-jobs.stages.photos.show', [$job, $install, $photo]))->assertOk();
    }

    public function test_unassigned_user_cannot_see_job_or_private_photo(): void
    {
        Storage::fake('local');
        $admin = $this->user('admin');
        $user = $this->user('user');
        $invoice = $this->mixedDraft($admin);
        $this->actingAs($admin)->post(route('invoices.issue', $invoice), ['issue_date' => today()->toDateString()]);

        $job = FieldJob::where('invoice_id', $invoice->id)->firstOrFail();
        $stage = $job->stages()->where('type', FieldJobStage::TYPE_INSTALL)->firstOrFail();
        $stored = UploadedFile::fake()->image('private.jpg')->store('field-jobs/testing', 'local');
        $photo = $stage->photos()->create([
            'path' => $stored,
            'original_name' => 'private.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => Storage::disk('local')->size($stored),
            'uploaded_by' => $admin->id,
        ]);

        $this->actingAs($user)->get(route('field-jobs.show', $job))->assertForbidden();
        $this->get(route('field-jobs.stages.photos.show', [$job, $stage, $photo]))->assertForbidden();
    }

    public function test_operational_roles_never_gain_financial_access_even_if_permission_is_attached(): void
    {
        $admin = $this->user('admin');
        $invoice = $this->mixedDraft($admin);

        foreach (['mandor', 'user'] as $role) {
            $account = $this->user($role);
            $account->role->permissions()->attach(Permission::where('name', 'invoicemenu')->firstOrFail());
            $this->actingAs($account)->get(route('invoices.show', $invoice))->assertForbidden();
        }
    }

    public function test_financial_notification_preferences_are_rejected_for_operational_roles(): void
    {
        $admin = $this->user('admin');
        $mandor = $this->user('mandor');

        $this->actingAs($admin)->post(route('notifications.preferences.store'), [
            'present_roles' => [$mandor->role_id],
            'prefs' => [$mandor->role_id => ['invoice_due', 'invoice_schedule_h7']],
        ])->assertRedirect();

        $this->assertFalse(NotificationPreference::where('role_id', $mandor->role_id)->where('type', 'invoice_due')->exists());
        $this->assertTrue(NotificationPreference::where('role_id', $mandor->role_id)->where('type', 'invoice_schedule_h7')->exists());
    }

    public function test_invoice_with_only_one_way_item_does_not_create_teardown_stage(): void
    {
        $admin = $this->user('admin');
        $client = Client::create(['name' => 'Client Transport']);
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'event_name' => 'Pengiriman Barang',
            'work_flow' => Invoice::FLOW_ONE_WAY,
            'status' => Invoice::STATUS_DRAFT,
            'created_by' => $admin->id,
        ]);
        $invoice->items()->create([
            'item_name' => 'Sewa truk', 'qty' => 1, 'unit_price' => 750_000,
        ]);
        $invoice->recalcTotalsAndStatus();

        $this->actingAs($admin)->post(route('invoices.issue', $invoice), ['issue_date' => today()->toDateString()]);
        $job = FieldJob::where('invoice_id', $invoice->id)->firstOrFail();

        $this->assertSame([FieldJobStage::TYPE_ONE_WAY], $job->stages()->where('is_active', true)->pluck('type')->all());
    }

    public function test_install_only_invoice_creates_no_teardown_stage(): void
    {
        $admin = $this->user('admin');
        $client = Client::create(['name' => 'Client Instalasi Permanen']);
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'event_name' => 'Instalasi Permanen',
            'work_flow' => Invoice::FLOW_INSTALL_ONLY,
            'status' => Invoice::STATUS_DRAFT,
            'created_by' => $admin->id,
        ]);
        $invoice->items()->createMany([
            ['item_name' => 'Backdrop', 'qty' => 1, 'unit_price' => 2_000_000],
            ['item_name' => 'Lampu', 'qty' => 4, 'unit_price' => 250_000],
        ]);

        $this->actingAs($admin)->post(route('invoices.issue', $invoice), ['issue_date' => today()->toDateString()]);
        $job = FieldJob::where('invoice_id', $invoice->id)->firstOrFail();

        $this->assertSame([FieldJobStage::TYPE_INSTALL], $job->stages()->where('is_active', true)->pluck('type')->all());
        $this->assertSame([Invoice::FLOW_INSTALL_ONLY], $job->items()->pluck('work_flow')->unique()->values()->all());
    }

    public function test_teardown_can_be_completed_without_a_photo_but_install_still_requires_one(): void
    {
        $admin = $this->user('admin');
        $invoice = $this->mixedDraft($admin);

        $this->actingAs($admin)->post(route('invoices.issue', $invoice), [
            'issue_date' => today()->toDateString(),
        ])->assertRedirect();

        $job = FieldJob::where('invoice_id', $invoice->id)->firstOrFail();
        $install = $job->stages()->where('type', FieldJobStage::TYPE_INSTALL)->firstOrFail();
        $teardown = $job->stages()->where('type', FieldJobStage::TYPE_TEARDOWN)->firstOrFail();

        $this->get(route('field-jobs.show', $job))
            ->assertOk()
            ->assertSee('Foto Bongkar bersifat opsional.');

        $this->patch(route('field-jobs.stages.update', [$job, $install]), [
            'status' => FieldJobStage::STATUS_COMPLETED,
        ])->assertRedirect()->assertSessionHas('error');
        $this->assertSame(FieldJobStage::STATUS_PENDING, $install->fresh()->status);

        $this->patch(route('field-jobs.stages.update', [$job, $teardown]), [
            'status' => FieldJobStage::STATUS_COMPLETED,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame(FieldJobStage::STATUS_COMPLETED, $teardown->fresh()->status);
        $this->assertSame(0, $teardown->photos()->count());
    }

    private function user(string $role): User
    {
        return User::where('email', "{$role}@immanuel.test")->firstOrFail();
    }

    private function mixedDraft(User $admin): Invoice
    {
        $client = Client::create(['name' => 'PT Operasional Aman']);
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'event_name' => 'Festival Nusantara',
            'location_event' => 'Lapangan Kota',
            'event_date' => today()->addDays(7),
            'loading_date' => now()->addDays(6),
            'bongkaran_date' => now()->addDays(8),
            'status' => Invoice::STATUS_DRAFT,
            'notes' => 'Catatan internal: pembayaran Rp 9.999.999',
            'operational_notes' => 'Instruksi aman untuk tim.',
            'work_flow' => Invoice::FLOW_INSTALL_TEARDOWN,
            'created_by' => $admin->id,
        ]);
        $invoice->items()->createMany([
            ['item_name' => 'Panggung', 'qty' => 1, 'unit_price' => 5_000_000],
            ['item_name' => 'Backdrop', 'qty' => 1, 'unit_price' => 2_000_000],
            ['item_name' => 'Lighting', 'qty' => 1, 'unit_price' => 1_000_000],
        ]);
        $invoice->recalcTotalsAndStatus();

        return $invoice->fresh();
    }
}
