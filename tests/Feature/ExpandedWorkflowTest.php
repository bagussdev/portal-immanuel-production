<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\FieldJob;
use App\Models\FieldJobStage;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Quotation;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExpandedWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_username_login_and_compact_user_form_are_available(): void
    {
        $user = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $user->update(['password' => Hash::make('Password123')]);

        $this->post('/login', ['login' => $user->username, 'password' => 'Password123'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $master = User::where('email', 'master@immanuel.test')->firstOrFail();
        $this->actingAs($master)->get(route('users.create'))
            ->assertOk()
            ->assertSeeText('Username')
            ->assertSeeText('Foto KTP')
            ->assertDontSee('capture=', false);
    }

    public function test_expansion_migration_can_resume_without_duplicating_backfilled_data(): void
    {
        $counts = [
            'quotation_locations' => DB::table('quotation_locations')->count(),
            'invoice_locations' => DB::table('invoice_locations')->count(),
            'field_job_sites' => DB::table('field_job_sites')->count(),
        ];

        $migration = require database_path('migrations/2026_08_25_000000_expand_documents_jobs_and_users.php');
        $migration->up();

        foreach ($counts as $table => $count) {
            $this->assertSame($count, DB::table($table)->count());
        }
    }

    public function test_user_photos_use_fixed_frames_and_ktp_rotation_is_saved(): void
    {
        Storage::fake('local');
        $master = User::where('email', 'master@immanuel.test')->firstOrFail();

        $this->actingAs($master)->post(route('users.store'), [
            'name' => 'Crew Foto',
            'username' => 'crew.foto',
            'email' => 'crew-foto@immanuel.test',
            'no_telf' => '081234567890',
            'role_id' => $master->role_id,
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'profile_photo' => UploadedFile::fake()->image('profil.jpg', 180, 240),
            'ktp_photo' => UploadedFile::fake()->image('ktp.jpg', 320, 200),
            'ktp_rotation' => 90,
        ])->assertRedirect(route('users.index'));

        $crew = User::where('email', 'crew-foto@immanuel.test')->firstOrFail();
        Storage::disk('local')->assertExists($crew->profile_photo_path);
        Storage::disk('local')->assertExists($crew->ktp_photo_path);
        [$width, $height] = getimagesize(Storage::disk('local')->path($crew->ktp_photo_path));
        $this->assertSame([200, 320], [$width, $height]);

        $this->get(route('users.edit', $crew))
            ->assertOk()
            ->assertSeeText('85,6 × 54 mm')
            ->assertSee('name="ktp_rotation"', false)
            ->assertSee('Putar KTP ke kanan', false);
        $this->get(route('users.index'))
            ->assertOk()
            ->assertSee(route('users.photo', [$crew, 'ktp']), false);

        $this->put(route('users.update', $crew), [
            'name' => $crew->name,
            'username' => $crew->username,
            'email' => $crew->email,
            'no_telf' => $crew->no_telf,
            'role_id' => $crew->role_id,
            'ktp_rotation' => 90,
        ])->assertRedirect(route('users.index'));

        [$width, $height] = getimagesize(Storage::disk('local')->path($crew->ktp_photo_path));
        $this->assertSame([320, 200], [$width, $height]);

        $pdfHtml = view('users.pdf', ['users' => collect([$crew->fresh()->load('role')])])->render();
        $this->assertStringContainsString('width:25mm;height:25mm', $pdfHtml);
        $this->assertStringContainsString('width:70mm;height:44.2mm', $pdfHtml);
    }

    public function test_invoice_supports_date_ranges_multiple_locations_and_direct_totals(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $start = today()->addDays(10);

        $this->actingAs($admin)->post(route('invoices.store'), [
            'client_name' => 'Pak Komang',
            'event_name' => 'Festival Multi Lokasi',
            'discount_mode' => 'amount',
            'tax_mode' => 'amount',
            'locations' => [
                [
                    'name' => 'The Meru Sanur',
                    'event_start_date' => $start->toDateString(),
                    'event_end_date' => $start->copy()->addDay()->toDateString(),
                    'loading_date' => $start->copy()->subDay()->setTime(8, 0)->format('Y-m-d H:i:s'),
                    'teardown_date' => $start->copy()->addDays(2)->setTime(20, 0)->format('Y-m-d H:i:s'),
                    'work_flow' => Invoice::FLOW_INSTALL_TEARDOWN,
                    'items' => [[
                        'item_name' => 'Panggung utama', 'qty' => 2, 'length' => 4,
                        'pricing_mode' => 'unit', 'unit_price' => '500000',
                    ]],
                ],
                [
                    'name' => 'Pantai Sanur',
                    'event_start_date' => $start->copy()->addDays(3)->toDateString(),
                    'work_flow' => Invoice::FLOW_ONE_WAY,
                    'items' => [[
                        'item_name' => 'Paket dekorasi', 'qty' => 12,
                        'pricing_mode' => 'total', 'line_total' => '3250000',
                    ]],
                ],
            ],
        ])->assertRedirect();

        $invoice = Invoice::latest('id')->firstOrFail();
        $this->assertSame(2, $invoice->locations()->count());
        $this->assertSame(7_250_000, (int) $invoice->fresh()->grand_total);
        $this->assertSame('total', $invoice->locations()->where('name', 'Pantai Sanur')->firstOrFail()->items()->firstOrFail()->pricing_mode);

        $this->post(route('invoices.issue', $invoice), [
            'issue_date' => today()->toDateString(),
            'due_date' => today()->addMonth()->toDateString(),
        ])->assertRedirect();

        $job = $invoice->fresh()->fieldJob()->with(['sites.stages'])->firstOrFail();
        $this->assertSame(2, $job->sites->count());
        $this->assertSame(3, $job->stages()->where('is_active', true)->count());
        $this->assertSame(1, $job->stages()->where('type', FieldJobStage::TYPE_INSTALL)->count());
        $this->assertSame(1, $job->stages()->where('type', FieldJobStage::TYPE_ONE_WAY)->count());

        $this->post(route('invoices.complete', $invoice))->assertRedirect();
        $this->assertSame(Invoice::STATUS_PAID, $invoice->fresh()->status);
        $this->assertSame(7_250_000, (int) $invoice->payments()->sum('amount'));

        $this->post(route('invoices.payments.store', $invoice), [
            'paid_at' => now()->toDateTimeString(), 'amount' => '100000', 'method' => 'Transfer',
        ])->assertRedirect();
        $this->assertSame(Invoice::STATUS_OVERPAID, $invoice->fresh()->status);
        $this->post(route('invoices.complete', $invoice), ['resolution_note' => 'Lebih bayar dikembalikan.'])->assertRedirect();
        $this->assertNotNull($invoice->fresh()->resolved_at);
        $this->get(route('invoices.index'))->assertOk()->assertDontSeeText('Festival Multi Lokasi');
        $this->get(route('invoices.index', ['history' => 1]))->assertOk()->assertSeeText('Festival Multi Lokasi');

        $job->stages()->update(['status' => FieldJobStage::STATUS_COMPLETED]);
        $job->recalculateStatus();
        $this->get(route('field-jobs.index'))->assertOk()->assertDontSeeText('Festival Multi Lokasi');
        $this->get(route('field-jobs.history'))->assertOk()->assertSeeText('Festival Multi Lokasi');
    }

    public function test_document_and_schedule_lists_sort_all_records_and_use_compact_item_editor(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $client = Client::create(['name' => 'Client Pengujian Sort']);

        foreach ([['Sort Event Lama', 3], ['Sort Event Baru', 20]] as [$event, $days]) {
            $invoice = Invoice::create([
                'client_id' => $client->id,
                'event_name' => $event,
                'event_date' => today()->addDays($days),
                'status' => Invoice::STATUS_DRAFT,
                'created_by' => $admin->id,
            ]);
            Quotation::create([
                'client_id' => $client->id,
                'event_name' => $event,
                'event_date' => today()->addDays($days),
                'quotation_date' => today()->addDays($days),
                'status' => Quotation::STATUS_DRAFT,
                'user_id' => $admin->id,
            ]);
            FieldJob::create([
                'invoice_id' => $invoice->id,
                'job_number' => 'JOB/SORT/'.$days,
                'client_name' => $client->name,
                'event_name' => $event,
                'event_date' => today()->addDays($days),
                'status' => FieldJob::STATUS_PENDING,
                'created_by' => $admin->id,
            ]);
        }

        $this->actingAs($admin)->get(route('invoices.index', ['sort' => 'event_date', 'direction' => 'asc']))
            ->assertOk()->assertSeeInOrder(['Sort Event Lama', 'Sort Event Baru'])->assertSeeText('Tgl invoice');
        $this->get(route('quotations.index', ['sort' => 'event_date', 'direction' => 'desc']))
            ->assertOk()->assertSeeInOrder(['Sort Event Baru', 'Sort Event Lama'])->assertSeeText('Tgl quotation');
        $this->get(route('field-jobs.index', ['sort' => 'event_date', 'direction' => 'asc']))
            ->assertOk()->assertSeeInOrder(['Sort Event Lama', 'Sort Event Baru'])->assertSeeText('Urutkan jadwal');
        $this->get(route('invoices.create'))
            ->assertOk()->assertSeeText('Simpan nama')->assertSee('editItemName(item)', false);
    }

    public function test_pay_all_only_marks_draft_slips_in_an_open_period(): void
    {
        $master = User::where('email', 'master@immanuel.test')->firstOrFail();
        $period = PayrollPeriod::create([
            'month' => now()->month,
            'year' => now()->year,
            'status' => PayrollPeriod::STATUS_OPEN,
            'open_by' => $master->id,
            'open_at' => now(),
        ]);
        foreach (User::where('active', true)->limit(3)->get() as $user) {
            Payroll::create([
                'payroll_period_id' => $period->id,
                'user_id' => $user->id,
                'status' => Payroll::STATUS_DRAFT,
                'total_base' => 1_000_000,
                'net_pay' => 1_000_000,
            ]);
        }

        $this->actingAs($master)->patch(route('payroll.period.pay-all', $period))->assertRedirect();

        $this->assertSame(0, $period->payrolls()->where('status', Payroll::STATUS_DRAFT)->count());
        $this->assertSame(3, $period->payrolls()->where('status', Payroll::STATUS_PAID)->count());
        $this->assertSame(3, $period->payrolls()->where('paid_by', $master->id)->count());
    }
}
