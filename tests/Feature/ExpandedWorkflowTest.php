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
use App\Services\FieldJobSynchronizer;
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
            'profile_crop_y' => 75,
            'profile_zoom' => 1.5,
            'ktp_crop_x' => 40,
            'ktp_crop_y' => 60,
            'ktp_zoom' => 1.4,
        ])->assertRedirect(route('users.index'));

        $crew = User::where('email', 'crew-foto@immanuel.test')->firstOrFail();
        Storage::disk('local')->assertExists($crew->profile_photo_path);
        Storage::disk('local')->assertExists($crew->ktp_photo_path);
        $this->assertSame([900, 900], array_slice(getimagesize(Storage::disk('local')->path($crew->profile_photo_path)), 0, 2));
        $this->assertSame([1284, 810], array_slice(getimagesize(Storage::disk('local')->path($crew->ktp_photo_path)), 0, 2));

        $this->get(route('users.edit', $crew))
            ->assertOk()
            ->assertSeeText('85,6 × 54 mm')
            ->assertSeeText('Geser vertikal')
            ->assertSee('name="profile_zoom"', false)
            ->assertSee('name="ktp_zoom"', false)
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
            'ktp_crop_x' => 50,
            'ktp_crop_y' => 50,
            'ktp_zoom' => 2,
            'ktp_transform_changed' => 1,
        ])->assertRedirect(route('users.index'));

        $this->assertSame([1284, 810], array_slice(getimagesize(Storage::disk('local')->path($crew->ktp_photo_path)), 0, 2));

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
            'event_date' => $start->toDateString(),
            'event_end_date' => $start->copy()->addDay()->toDateString(),
            'discount_mode' => 'amount',
            'tax_mode' => 'amount',
            'locations' => [
                [
                    'name' => 'The Meru Sanur',
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
                    'work_flow' => Invoice::FLOW_ONE_WAY,
                    'items' => [[
                        'item_name' => 'Paket dekorasi', 'qty' => 12,
                        'pricing_mode' => 'total', 'unit_price' => '300000', 'line_total' => '3250000',
                    ]],
                ],
            ],
        ])->assertRedirect();

        $invoice = Invoice::latest('id')->firstOrFail();
        $this->assertSame(2, $invoice->locations()->count());
        $this->assertSame($start->toDateString(), $invoice->event_date->toDateString());
        $this->assertSame($start->copy()->addDay()->toDateString(), $invoice->event_end_date->toDateString());
        $this->assertTrue($invoice->locations->every(fn ($location) => $location->event_start_date->isSameDay($start)));
        $this->assertSame(7_250_000, (int) $invoice->fresh()->grand_total);
        $directTotalItem = $invoice->locations()->where('name', 'Pantai Sanur')->firstOrFail()->items()->firstOrFail();
        $this->assertSame('total', $directTotalItem->pricing_mode);
        $this->assertSame(300_000, (int) $directTotalItem->unit_price);
        $this->assertSame(3_250_000, (int) $directTotalItem->total);

        $invoice->load(['client', 'bankDetail', 'locations.items', 'items', 'payments']);
        $detailResponse = $this->get(route('invoices.show', $invoice))->assertOk();
        $this->assertSame(2, substr_count($detailResponse->getContent(), 'data-location-disclosure'));
        $detailResponse->assertSee('data-mobile-open="false"', false)
            ->assertSee('whitespace-nowrap text-right font-extrabold tabular-nums', false);
        $multiLocationHtml = view('invoices.pdf', compact('invoice'))->render();
        $this->assertSame(2, substr_count($multiLocationHtml, '<div class="location-head">'));
        $this->assertStringNotContainsString('Loading:', $multiLocationHtml);
        $this->assertStringNotContainsString('Bongkar:', $multiLocationHtml);
        $this->assertStringNotContainsString('08:00', $multiLocationHtml);
        $this->assertStringNotContainsString('20:00', $multiLocationHtml);
        $this->assertStringNotContainsString('Acara:', $multiLocationHtml);
        $this->assertSame(1, substr_count($multiLocationHtml, 'Invoice No'));
        $this->assertSame(1, substr_count($multiLocationHtml, 'Invoice Date'));
        $this->assertStringNotContainsString('document-number', $multiLocationHtml);
        $this->assertStringContainsString('Total Tagihan', $multiLocationHtml);
        $this->assertStringContainsString('Sisa Tagihan', $multiLocationHtml);

        $invoice->setRelation('locations', $invoice->locations->take(1));
        $singleLocationHtml = view('invoices.pdf', compact('invoice'))->render();
        $this->assertSame(0, substr_count($singleLocationHtml, '<div class="location-head">'));
        $this->assertStringNotContainsString('Jumlah lokasi', $singleLocationHtml);
        $this->assertStringContainsString('The Meru Sanur', $singleLocationHtml);
        $invoice->unsetRelation('locations');

        $this->post(route('invoices.issue', $invoice), [
            'issue_date' => today()->toDateString(),
            'due_date' => today()->addMonth()->toDateString(),
        ])->assertRedirect();

        $jobs = $invoice->fresh()->fieldJobs()->with(['sites', 'stages', 'items'])->get();
        $this->assertCount(2, $jobs);
        $this->assertTrue($jobs->every(fn (FieldJob $job) => $job->sites->count() === 1));
        $this->assertSame(['The Meru Sanur', 'Pantai Sanur'], $jobs->pluck('location')->all());
        $this->assertSame(3, $jobs->sum(fn (FieldJob $job) => $job->stages->where('is_active', true)->count()));
        $this->assertSame(1, $jobs->flatMap->stages->where('type', FieldJobStage::TYPE_INSTALL)->count());
        $this->assertSame(1, $jobs->flatMap->stages->where('type', FieldJobStage::TYPE_ONE_WAY)->count());
        $this->assertSame(['Panggung utama'], $jobs->firstWhere('location', 'The Meru Sanur')->items->pluck('item_name')->all());
        $this->assertSame(['Paket dekorasi'], $jobs->firstWhere('location', 'Pantai Sanur')->items->pluck('item_name')->all());

        $scheduleList = $this->get(route('field-jobs.index', ['invoice_id' => $invoice->id]))->assertOk();
        $scheduleList->assertSeeText('The Meru Sanur')->assertSeeText('Pantai Sanur');
        foreach ($jobs as $job) {
            $scheduleList->assertSee(route('field-jobs.show', $job), false);
        }
        $this->get(route('field-jobs.show', $jobs->first()))
            ->assertOk()
            ->assertSeeText('Item pekerjaan')
            ->assertSeeText('Panggung utama')
            ->assertDontSeeText('Harga satuan')
            ->assertDontSeeText('Rp 500.000');

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

        foreach ($jobs as $job) {
            $job->stages()->update(['status' => FieldJobStage::STATUS_COMPLETED]);
            $job->recalculateStatus();
        }
        $this->get(route('field-jobs.index'))->assertOk()->assertDontSeeText('Festival Multi Lokasi');
        $this->get(route('field-jobs.history'))->assertOk()->assertSeeText('Festival Multi Lokasi');
    }

    public function test_document_and_schedule_lists_use_simple_order_filters_and_compact_item_editor(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $client = Client::create(['name' => 'Client Pengujian Sort']);

        foreach ([['Sort Event Lama', 3], ['Sort Event Baru', 20]] as [$event, $days]) {
            $isOlderRecord = $event === 'Sort Event Lama';
            $createdAt = now()->subDays($isOlderRecord ? 2 : 1);
            $invoice = Invoice::create([
                'client_id' => $client->id,
                'event_name' => $event,
                'event_date' => today()->addDays($days),
                'issue_date' => today()->addDays($isOlderRecord ? 60 : 1),
                'status' => Invoice::STATUS_DRAFT,
                'created_by' => $admin->id,
            ]);
            $invoice->forceFill(['created_at' => $createdAt])->saveQuietly();
            $quotation = Quotation::create([
                'client_id' => $client->id,
                'event_name' => $event,
                'event_date' => today()->addDays($days),
                'quotation_date' => today()->addDays($isOlderRecord ? 60 : 1),
                'status' => Quotation::STATUS_DRAFT,
                'user_id' => $admin->id,
            ]);
            $quotation->forceFill(['created_at' => $createdAt])->saveQuietly();
            $fieldJob = FieldJob::create([
                'invoice_id' => $invoice->id,
                'job_number' => 'JOB/SORT/'.$days,
                'client_name' => $client->name,
                'event_name' => $event,
                'event_date' => today()->addDays($days),
                'loading_date' => today()->addDays($isOlderRecord ? 60 : 1),
                'status' => FieldJob::STATUS_PENDING,
                'created_by' => $admin->id,
            ]);
            $fieldJob->forceFill(['created_at' => $createdAt])->saveQuietly();
        }

        $this->actingAs($admin)->get(route('invoices.index', ['order' => 'oldest']))
            ->assertOk()->assertSeeInOrder(['Sort Event Lama', 'Sort Event Baru'])
            ->assertSeeText('Terbaru')->assertSeeText('Terlama')->assertSeeText('Tgl invoice');
        $this->get(route('invoices.index', ['order' => 'latest', 'search' => 'Sort Event']))
            ->assertOk()->assertSee('href="'.route('invoices.index').'?'.http_build_query(['search' => 'Sort Event']).'"', false);
        $this->get(route('quotations.index', ['order' => 'latest']))
            ->assertOk()->assertSeeInOrder(['Sort Event Baru', 'Sort Event Lama'])
            ->assertSeeText('Terbaru')->assertSeeText('Terlama')->assertSeeText('Tgl quotation');
        $this->get(route('field-jobs.index', ['order' => 'oldest']))
            ->assertOk()->assertSeeInOrder(['Sort Event Lama', 'Sort Event Baru'])
            ->assertSeeText('Terbaru')->assertSeeText('Terlama');
        $this->get(route('field-jobs.index', ['order' => 'latest']))
            ->assertOk()->assertSeeInOrder(['Sort Event Baru', 'Sort Event Lama']);
        $this->get(route('invoices.create'))
            ->assertOk()
            ->assertSeeText('Simpan nama')
            ->assertSee('editItemName(item)', false)
            ->assertSeeText('Harga satuan')
            ->assertSeeText('Total')
            ->assertDontSee('<span>Mode</span>', false)
            ->assertSee('setLineTotal(item', false)
            ->assertSee('min-w-[550px]', false)
            ->assertSee('items-center justify-center p-4', false)
            ->assertDontSee('items-end justify-center', false)
            ->assertSee('name="event_date"', false)
            ->assertSee('name="event_end_date"', false)
            ->assertSeeText('Terapkan ke semua lokasi')
            ->assertSeeText('Jadwal event')
            ->assertSeeText('Pindahkan')
            ->assertSee('data-item-list', false)
            ->assertSee('data-mobile-open="false"', false)
            ->assertSee('window.Sortable.create', false)
            ->assertDontSee('event_start_date', false);
    }

    public function test_legacy_multi_site_job_is_split_without_losing_assignments(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $crew = User::where('email', 'user@immanuel.test')->firstOrFail();
        $client = Client::create(['name' => 'Client Jadwal Lama']);
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'event_name' => 'Event Jadwal Lama',
            'event_date' => today()->addWeek(),
            'status' => Invoice::STATUS_UNPAID,
            'created_by' => $admin->id,
        ]);
        $locations = collect(['Lokasi Lama A', 'Lokasi Lama B'])->map(function (string $name, int $index) use ($invoice) {
            $location = $invoice->locations()->create([
                'name' => $name,
                'event_start_date' => $invoice->event_date,
                'loading_date' => now()->addDays(5 + $index),
                'work_flow' => Invoice::FLOW_INSTALL_ONLY,
                'sort_order' => $index,
            ]);
            $location->items()->create([
                'invoice_id' => $invoice->id,
                'item_name' => 'Item '.$name,
                'qty' => 1,
                'unit_price' => 100_000,
            ]);

            return $location;
        });

        $legacyJob = FieldJob::create([
            'invoice_id' => $invoice->id,
            'job_number' => 'JOB/LEGACY/001',
            'client_name' => $client->name,
            'event_name' => $invoice->event_name,
            'status' => FieldJob::STATUS_PENDING,
            'created_by' => $admin->id,
        ]);

        foreach ($locations as $location) {
            $site = $legacyJob->sites()->create([
                'invoice_location_id' => $location->id,
                'name' => $location->name,
                'event_start_date' => $location->event_start_date,
                'loading_date' => $location->loading_date,
                'work_flow' => $location->work_flow,
                'sort_order' => $location->sort_order,
            ]);
            $site->items()->create([
                'field_job_id' => $legacyJob->id,
                'invoice_item_id' => $location->items()->value('id'),
                'item_name' => 'Item '.$location->name,
                'qty' => 1,
                'work_flow' => Invoice::FLOW_INSTALL_ONLY,
            ]);
            $stage = $site->stages()->create([
                'field_job_id' => $legacyJob->id,
                'type' => FieldJobStage::TYPE_INSTALL,
                'scheduled_at' => $location->loading_date,
                'status' => FieldJobStage::STATUS_PENDING,
                'is_active' => true,
            ]);
            if ($location->name === 'Lokasi Lama B') {
                $stage->assignees()->attach($crew->id, ['assigned_by' => $admin->id]);
                $stage->photos()->create([
                    'path' => 'field-jobs/legacy/foto.jpg',
                    'original_name' => 'foto.jpg',
                    'mime_type' => 'image/jpeg',
                    'size_bytes' => 1024,
                    'uploaded_by' => $crew->id,
                ]);
            }
        }

        app(FieldJobSynchronizer::class)->sync($invoice, $admin->id);

        $jobs = $invoice->fieldJobs()->with(['sites', 'items', 'stages.assignees'])->get();
        $this->assertCount(2, $jobs);
        $this->assertTrue($jobs->every(fn (FieldJob $job) => $job->sites->count() === 1));
        $secondStage = $jobs->firstWhere('location', 'Lokasi Lama B')->stages->first();
        $this->assertTrue($secondStage->assignees->contains('id', $crew->id));
        $this->assertSame(1, $secondStage->photos()->count());
    }

    public function test_document_items_keep_drag_order_when_moved_between_locations(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $payload = [
            'client_name' => 'Client Drag Item',
            'event_name' => 'Uji Urutan Item',
            'event_date' => today()->addWeek()->toDateString(),
            'discount_mode' => 'amount',
            'tax_mode' => 'amount',
            'locations' => [
                [
                    'name' => 'Lokasi Satu',
                    'work_flow' => Invoice::FLOW_INSTALL_TEARDOWN,
                    'items' => [
                        ['item_name' => 'Item Alpha', 'qty' => 1, 'pricing_mode' => 'total', 'line_total' => '100000'],
                        ['item_name' => 'Item Beta', 'qty' => 1, 'pricing_mode' => 'total', 'line_total' => '200000'],
                    ],
                ],
                [
                    'name' => 'Lokasi Dua',
                    'work_flow' => Invoice::FLOW_ONE_WAY,
                    'items' => [
                        ['item_name' => 'Item Gamma', 'qty' => 1, 'pricing_mode' => 'total', 'line_total' => '300000'],
                    ],
                ],
            ],
        ];

        $this->actingAs($admin)->post(route('invoices.store'), $payload)->assertRedirect();
        $invoice = Invoice::latest('id')->firstOrFail();

        $payload['locations'][0]['items'] = [];
        $payload['locations'][1]['items'] = [
            ['item_name' => 'Item Beta', 'qty' => 1, 'pricing_mode' => 'total', 'line_total' => '200000'],
            ['item_name' => 'Item Alpha', 'qty' => 1, 'pricing_mode' => 'total', 'line_total' => '100000'],
            ['item_name' => 'Item Gamma', 'qty' => 1, 'pricing_mode' => 'total', 'line_total' => '300000'],
        ];

        $this->put(route('invoices.update', $invoice), $payload)->assertRedirect();

        $locations = $invoice->fresh()->locations()->with('items')->get();
        $this->assertCount(0, $locations[0]->items);
        $this->assertSame(['Item Beta', 'Item Alpha', 'Item Gamma'], $locations[1]->items->pluck('item_name')->all());
        $this->assertSame(600_000, (int) $invoice->fresh()->grand_total);

        $this->post(route('invoices.issue', $invoice), [
            'issue_date' => today()->toDateString(),
            'due_date' => today()->addMonth()->toDateString(),
        ])->assertRedirect();
        $jobs = $invoice->fresh()->fieldJobs()->with('items')->get();
        $this->assertCount(2, $jobs);
        $this->assertSame([], $jobs->firstWhere('location', 'Lokasi Satu')->items->pluck('item_name')->all());
        $this->assertSame(
            ['Item Beta', 'Item Alpha', 'Item Gamma'],
            $jobs->firstWhere('location', 'Lokasi Dua')->items->pluck('item_name')->all(),
        );
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
