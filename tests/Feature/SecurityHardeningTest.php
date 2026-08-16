<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\ExpensePeriod;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_dashboard_uses_white_logo_without_changing_invoice_logo(): void
    {
        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('assets/brand/immanuel-production-white-logo.png', false);

        $invoiceTemplate = file_get_contents(resource_path('views/invoices/pdf.blade.php'));
        $this->assertStringContainsString("public_path('assets/logo.png')", $invoiceTemplate);
        $this->assertStringNotContainsString('immanuel-production-white-logo.png', $invoiceTemplate);
    }

    public function test_private_storage_has_no_generic_public_route(): void
    {
        $this->assertFalse(app('router')->has('storage.local'));
        $this->assertFalse(app('router')->has('storage.local.upload'));
    }

    public function test_legacy_expense_attachment_is_moved_to_private_storage_and_authorized(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $admin = User::where('email', 'admin@immanuel.test')->firstOrFail();
        $operationalUser = User::where('email', 'user@immanuel.test')->firstOrFail();
        ExpensePeriod::ensureOpen((int) now()->year, (int) now()->month);

        $path = 'expenses/private-proof.pdf';
        Storage::disk('public')->put($path, 'private financial document');

        $expense = Expense::create([
            'expense_date' => today()->toDateString(),
            'name' => 'Keperluan operasional',
            'qty' => 1,
            'total' => 100_000,
            'attachment' => $path,
            'created_by' => $admin->id,
        ]);

        $this->artisan('expenses:secure-attachments')->assertSuccessful();
        Storage::disk('local')->assertExists($path);
        Storage::disk('public')->assertMissing($path);

        $this->actingAs($operationalUser)->get(route('expenses.attachment', $expense))->assertForbidden();
        $this->actingAs($admin)->get(route('expenses.attachment', $expense))
            ->assertOk()
            ->assertDownload('private-proof.pdf');
    }
}
