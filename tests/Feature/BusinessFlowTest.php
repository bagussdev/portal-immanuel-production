<?php

namespace Tests\Feature;

use App\Models\BankDetail;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Quotation;
use App\Models\User;
use App\Services\ApproveQuotation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_each_role_can_boot_its_dashboard(): void
    {
        foreach (['master', 'admin', 'mandor', 'user'] as $role) {
            $account = User::where('email', "{$role}@immanuel.test")->firstOrFail();
            $response = $this->actingAs($account)->get(route('dashboard'))->assertOk();
            $response->assertDontSee(route('client.index'), false);
            $response->assertSee('Jadwal Event')
                ->assertDontSee('Bongkaran dan Pasangan')
                ->assertSee(route('field-jobs.index'), false)
                ->assertDontSee(route('schedule.index'), false);
        }
    }

    public function test_user_phone_number_is_not_limited_to_32_characters(): void
    {
        $master = User::where('email', 'master@immanuel.test')->firstOrFail();
        $role = $master->role;
        $phone = str_repeat('1234567890', 5);

        $response = $this->actingAs($master)->post(route('users.store'), [
            'name' => 'Nomor Panjang',
            'email' => 'nomor-panjang@immanuel.test',
            'no_telf' => $phone,
            'role_id' => $role->id,
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'nomor-panjang@immanuel.test',
            'no_telf' => $phone,
        ]);
    }

    public function test_approved_quotation_becomes_an_editable_invoice_draft_with_deductive_tax(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $client = Client::create(['name' => 'PT Demo Client']);
        $quotation = Quotation::create([
            'client_id' => $client->id,
            'user_id' => $admin->id,
            'quotation_date' => today(),
            'event_name' => 'Demo Event',
            'status' => Quotation::STATUS_SENT,
            'subtotal' => 1_000_000,
            'discount_percent' => 10,
            'discount' => 100_000,
            'tax_percent' => 2.5,
            'tax_value' => 22_500,
            'grand_total' => 877_500,
        ]);
        $quotation->items()->create([
            'item_name' => 'Paket Produksi',
            'qty' => 1,
            'unit_price' => 1_000_000,
            'total' => 1_000_000,
        ]);

        $this->actingAs($admin);
        $invoice = app(ApproveQuotation::class)->handle($quotation, $admin->id)->fresh();

        $this->assertSame(Quotation::STATUS_APPROVED, $quotation->fresh()->status);
        $this->assertSame(Invoice::STATUS_DRAFT, $invoice->status);
        $this->assertNull($invoice->invoice_number);
        $this->assertSame(1_000_000, (int) $invoice->subtotal);
        $this->assertSame(100_000, (int) $invoice->discount_value);
        $this->assertSame(22_500, (int) $invoice->tax_value);
        $this->assertSame(877_500, (int) $invoice->grand_total);
        $this->get(route('invoices.edit', $invoice))->assertOk();
        $this->get(route('invoices.show', $invoice))->assertOk()->assertSee('Detail invoice');
        $this->get(route('quotations.show', $quotation))->assertOk()->assertSee('Detail quotation');
    }

    public function test_an_issued_invoice_accepts_multiple_payments_and_void_recalculates_balance(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $invoice = $this->makeDraftInvoice($admin);

        $this->actingAs($admin)
            ->post(route('invoices.issue', $invoice), [
                'issue_date' => today()->toDateString(),
                'due_date' => today()->addDays(14)->toDateString(),
            ])->assertRedirect();

        $invoice->refresh();
        $this->assertSame(Invoice::STATUS_UNPAID, $invoice->status);
        $this->assertNotNull($invoice->invoice_number);

        foreach ([400_000, 477_500, 500] as $amount) {
            $this->post(route('invoices.payments.store', $invoice), [
                'paid_at' => now()->toDateTimeString(),
                'amount' => (string) $amount,
                'method' => 'transfer',
            ])->assertRedirect();
        }

        $invoice->refresh();
        $this->assertSame(3, $invoice->payments()->count());
        $this->assertSame(878_000, (int) $invoice->total_paid);
        $this->assertSame(0, (int) $invoice->balance_due);
        $this->assertSame(Invoice::STATUS_OVERPAID, $invoice->status);

        $lastPayment = $invoice->payments()->latest('id')->firstOrFail();
        $this->patch(route('invoices.payments.void', [$invoice, $lastPayment]), [
            'reason' => 'Salah input nominal saat rekonsiliasi.',
        ])->assertRedirect();

        $invoice->refresh();
        $this->assertNotNull($lastPayment->fresh()->voided_at);
        $this->assertSame(877_500, (int) $invoice->total_paid);
        $this->assertSame(Invoice::STATUS_PAID, $invoice->status);
    }

    public function test_editing_invoice_total_recalculates_active_payment_percentages(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $invoice = $this->makeDraftInvoice($admin);

        $this->actingAs($admin)->post(route('invoices.issue', $invoice), [
            'issue_date' => today()->toDateString(),
            'due_date' => today()->addDays(14)->toDateString(),
        ])->assertRedirect();

        $this->post(route('invoices.payments.store', $invoice), [
            'paid_at' => now()->toDateTimeString(),
            'amount' => '400000',
            'method' => 'transfer',
        ])->assertRedirect();

        $payment = $invoice->payments()->firstOrFail();
        $this->assertSame('45.58', $payment->percent);

        $this->patch(route('invoices.update', $invoice), [
            'client_name' => $invoice->client->name,
            'event_name' => $invoice->event_name,
            'work_flow' => Invoice::FLOW_INSTALL_TEARDOWN,
            'discount_mode' => 'percent',
            'discount_percent' => 10,
            'tax_mode' => 'percent',
            'tax_percent' => 2.5,
            'items' => [[
                'item_name' => 'Paket Produksi',
                'qty' => 1,
                'length' => '',
                'unit_price' => '2000000',
            ]],
        ])->assertRedirect(route('invoices.show', $invoice));

        $this->assertSame(1_755_000, (int) $invoice->fresh()->grand_total);
        $this->assertSame('22.79', $payment->fresh()->percent);
        $this->assertSame(400_000, (int) $payment->fresh()->amount);
        $this->get(route('payments.index'))
            ->assertOk()
            ->assertSee('23% —', false)
            ->assertSeeText('400.000');
    }

    public function test_invoice_can_create_a_client_directly_from_the_document_form(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('invoices.store'), [
            'client_name' => '  PT Client Baru  ',
            'event_name' => 'Peluncuran Produk',
            'event_date' => today()->addWeek()->toDateString(),
            'work_flow' => Invoice::FLOW_ONE_WAY,
            'discount_mode' => 'amount',
            'discount_value' => '100000',
            'tax_mode' => 'percent',
            'tax_percent' => 2.5,
            'items' => [[
                'item_name' => 'Paket Produksi',
                'qty' => 1,
                'length' => '',
                'unit_price' => '1000000',
            ]],
        ]);

        $invoice = Invoice::latest('id')->firstOrFail();
        $response->assertRedirect(route('invoices.edit', $invoice));
        $this->assertSame('PT Client Baru', $invoice->client->name);
        $this->assertSame(Invoice::FLOW_ONE_WAY, $invoice->work_flow);
        $this->assertSame(877_500, (int) $invoice->grand_total);
        $this->assertNull($invoice->items->first()->length);
        $this->get(route('invoices.edit', $invoice))
            ->assertOk()
            ->assertSeeText('Tipe pekerjaan')
            ->assertDontSee('data-name="work_flow"', false);
    }

    public function test_bank_details_and_grouped_prices_flow_from_quotation_to_invoice(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $bankDetail = BankDetail::where('label', 'Sugito')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('quotations.store'), [
            'client_name' => 'Bapak Widhi',
            'event_name' => 'Puri Begawan Tambahan',
            'bank_detail_id' => $bankDetail->id,
            'discount_value' => '0',
            'tax_value' => '0',
            'items' => [
                ['item_name' => 'Tangga 1 15 Trap', 'qty' => 1, 'length' => '', 'unit_price' => '20000000'],
                ['item_name' => 'Tangga 2 13 Trep (3 Set)', 'qty' => 3, 'length' => '', 'unit_price' => '0', 'merge_price' => '1'],
                ['item_name' => 'Barikade 30m', 'qty' => 1, 'length' => '', 'unit_price' => '3000000'],
            ],
        ]);

        $quotation = Quotation::latest('id')->firstOrFail();
        $response->assertRedirect(route('quotations.show', $quotation));
        $this->assertSame($bankDetail->id, $quotation->bank_detail_id);
        $this->assertSame(23_000_000, (int) $quotation->subtotal);
        $this->assertTrue($quotation->items->every(fn ($item) => $item->length === null));
        $this->assertSame(20_000_000, (int) $quotation->items[0]->total);
        $this->assertSame(0, (int) $quotation->items[1]->total);
        $this->assertSame($quotation->items[0]->price_group, $quotation->items[1]->price_group);

        $this->get(route('quotations.show', $quotation))->assertOk()->assertSee('Zakharia Sugito Kurniawan');
        $this->get(route('quotations.export.pdf', $quotation))->assertOk();
        $invoice = app(ApproveQuotation::class)->handle($quotation, $admin->id)->fresh();
        $this->assertSame($bankDetail->id, $invoice->bank_detail_id);
        $this->assertSame(23_000_000, (int) $invoice->subtotal);
        $this->assertSame($invoice->items[0]->price_group, $invoice->items[1]->price_group);
        $this->get(route('invoices.show', $invoice))->assertOk()->assertSee('0490392947');

        $invoice->update(['notes' => 'Catatan invoice profesional.']);
        $invoice->load(['client', 'bankDetail', 'items', 'payments']);
        $invoiceHtml = view('invoices.pdf', compact('invoice'))->render();
        $this->assertStringContainsString(public_path('assets/logo.png'), $invoiceHtml);
        $this->assertStringNotContainsString('immanuel-production-legacy-logo.png', $invoiceHtml);
        $this->assertTrue(
            strpos($invoiceHtml, 'Catatan invoice profesional.') < strpos($invoiceHtml, 'Detail Rekening')
            && strpos($invoiceHtml, 'Detail Rekening') < strpos($invoiceHtml, 'Ringkasan')
        );

        $quotation->update(['description' => 'Catatan quotation profesional.']);
        $quotation->load(['client', 'bankDetail', 'items']);
        $quotationHtml = view('quotations.pdf', compact('quotation'))->render();
        $this->assertStringContainsString(public_path('assets/logo.png'), $quotationHtml);
        $this->assertStringNotContainsString('immanuel-production-legacy-logo.png', $quotationHtml);
        $this->assertTrue(
            strpos($quotationHtml, 'Catatan quotation profesional.') < strpos($quotationHtml, 'Detail Rekening')
            && strpos($quotationHtml, 'Detail Rekening') < strpos($quotationHtml, 'Ringkasan')
        );
    }

    public function test_admin_can_manage_bank_details_but_cannot_delete_them(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $bankDetail = BankDetail::where('label', 'Yayak')->firstOrFail();

        $this->actingAs($admin)->get(route('bank-details.index'))->assertOk()->assertSee('Sugito');
        $this->get(route('bank-details.edit', $bankDetail))->assertOk();
        $this->delete(route('bank-details.destroy', $bankDetail))->assertForbidden();
    }

    public function test_legacy_calendar_routes_redirect_to_the_field_job_schedule(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $invoice = $this->makeDraftInvoice($admin);
        $invoice->update(['event_date' => today()->addDays(3)]);

        $this->actingAs($admin)->post(route('invoices.issue', $invoice), [
            'issue_date' => today()->toDateString(),
            'due_date' => today()->addDays(14)->toDateString(),
        ])->assertRedirect();

        $invoice->refresh();
        $fieldJob = $invoice->fieldJob()->firstOrFail();

        $this->get(route('schedule.index'))
            ->assertRedirect(route('field-jobs.index'));
        $this->get(route('schedule.show', $invoice))
            ->assertRedirect(route('field-jobs.show', $fieldJob));
        $this->get('/schedule/events')->assertNotFound();
        $this->get('/schedule/loading-badges')->assertNotFound();

        foreach (['mandor', 'user'] as $role) {
            $account = User::where('email', "{$role}@immanuel.test")->firstOrFail();
            if ($role === 'user') {
                $fieldJob->activeStages()->firstOrFail()->assignees()->attach($account->id, [
                    'assigned_by' => $admin->id,
                ]);
            }

            $this->actingAs($account)->get(route('schedule.index'))
                ->assertRedirect(route('field-jobs.index'));
            $this->followingRedirects()->get(route('schedule.show', $invoice))
                ->assertOk()
                ->assertDontSee($invoice->invoice_number);
            $this->get(route('invoices.show', $invoice))->assertForbidden();
        }
    }

    public function test_payroll_visibility_and_direct_payment_follow_role_rules_and_closing(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $mandor = User::where('email', 'mandor@immanuel.test')->firstOrFail();
        $user = User::where('email', 'user@immanuel.test')->firstOrFail();
        $master = User::where('email', 'master@immanuel.test')->firstOrFail();
        $period = PayrollPeriod::create([
            'month' => now()->month,
            'year' => now()->year,
            'status' => PayrollPeriod::STATUS_OPEN,
            'open_by' => $admin->id,
            'open_at' => now(),
        ]);
        $ownSlip = $this->makePayroll($period, $user);
        $otherSlip = $this->makePayroll($period, $mandor);

        $this->actingAs($user)->get(route('payroll.show', $ownSlip))->assertOk();
        $this->get(route('payroll.show', $otherSlip))->assertForbidden();

        $this->actingAs($mandor)->get(route('payroll.edit', $ownSlip))->assertOk();
        $this->patch(route('payroll.pay', $ownSlip))->assertForbidden();

        $this->actingAs($admin)->patch(route('payroll.pay', $ownSlip))->assertRedirect();
        $this->assertSame(Payroll::STATUS_PAID, $ownSlip->fresh()->status);
        $this->get(route('payroll.slip.pdf', $ownSlip))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->patch(route('payroll.period.close', $period))->assertRedirect();
        $this->assertSame(PayrollPeriod::STATUS_OPEN, $period->fresh()->status);

        $this->patch(route('payroll.pay', $otherSlip))->assertRedirect();
        $this->actingAs($master)->patch(route('payroll.period.close', $period))->assertRedirect();
        $this->assertSame(PayrollPeriod::STATUS_CLOSED, $period->fresh()->status);
    }

    private function makeDraftInvoice(User $creator): Invoice
    {
        $client = Client::create(['name' => 'PT Pembayaran Bertahap']);
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'event_name' => 'Event Pembayaran',
            'status' => Invoice::STATUS_DRAFT,
            'discount_percent' => 10,
            'tax_percent' => 2.5,
            'created_by' => $creator->id,
        ]);
        $invoice->items()->create([
            'item_name' => 'Paket Produksi',
            'qty' => 1,
            'unit_price' => 1_000_000,
        ]);
        $invoice->recalcTotalsAndStatus();

        return $invoice->fresh();
    }

    private function makePayroll(PayrollPeriod $period, User $user): Payroll
    {
        $payroll = Payroll::create([
            'payroll_period_id' => $period->id,
            'user_id' => $user->id,
            'status' => Payroll::STATUS_DRAFT,
        ]);
        $payroll->items()->create(['type' => 'base', 'name' => 'Gaji Pokok', 'amount' => 1_000_000]);
        $payroll->recalcTotals();

        return $payroll->fresh();
    }
}
