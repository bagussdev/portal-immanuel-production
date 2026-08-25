<?php

use App\Http\Controllers\ArmadaController;
use App\Http\Controllers\BankDetailController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FieldJobController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    Route::get('armada/rows', [ArmadaController::class, 'rows'])->name('armada.rows');
    Route::get('armada/sync/changes', [ArmadaController::class, 'changes'])->name('armada.sync.changes');
    Route::post('armada/{armada}/samsat', [ArmadaController::class, 'processSamsat'])->name('armada.samsat.process');
    Route::get('armada/{armada}/stnk', [ArmadaController::class, 'stnkAttachment'])->name('armada.stnk.attachment');
    Route::get('armada/{armada}/renewals/{renewal}/attachment', [ArmadaController::class, 'renewalAttachment'])->name('armada.renewals.attachment');
    Route::resource('armada', ArmadaController::class);

    Route::get('equipment/rows', [EquipmentController::class, 'rows'])->name('equipment.rows');
    Route::get('equipment/sync/changes', [EquipmentController::class, 'changes'])->name('equipment.sync.changes');
    Route::resource('equipment', EquipmentController::class);

    Route::get('client/rows', [ClientController::class, 'rows'])->name('client.rows');
    Route::get('client/sync/changes', [ClientController::class, 'changes'])->name('client.sync.changes');
    Route::resource('client', ClientController::class);
    Route::resource('gudang', GudangController::class);
    Route::resource('bank-details', BankDetailController::class)->except('show');

    Route::get('quotations/rows', [QuotationController::class, 'rows'])->name('quotations.rows');
    Route::get('quotations/sync/changes', [QuotationController::class, 'changes'])->name('quotations.sync.changes');
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'exportPdf'])->name('quotations.export.pdf');
    Route::post('quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.acc');
    Route::post('quotations/{quotation}/cancel', [QuotationController::class, 'cancel'])->name('quotations.cancel');
    Route::resource('quotations', QuotationController::class);

    Route::get('invoices/rows', [InvoiceController::class, 'rows'])->name('invoices.rows');
    Route::get('invoices/sync/changes', [InvoiceController::class, 'changes'])->name('invoices.sync.changes');
    Route::post('invoices/{invoice}/issue', [InvoiceController::class, 'issue'])->name('invoices.issue');
    Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'storePayment'])->name('invoices.payments.store');
    Route::post('invoices/{invoice}/complete', [InvoiceController::class, 'complete'])->name('invoices.complete');
    Route::patch('invoices/{invoice}/payments/{payment}/void', [InvoiceController::class, 'voidPayment'])->name('invoices.payments.void');
    Route::get('invoices/{invoice}/payments/{payment}/attachment', [InvoiceController::class, 'paymentAttachment'])->name('invoices.payments.attachment');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.export.pdf');
    Route::patch('invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void');
    Route::resource('invoices', InvoiceController::class);

    Route::get('payroll/sync/changes', [PayrollController::class, 'changes'])->name('payroll.sync.changes');
    Route::get('payroll/rows', [PayrollController::class, 'rows'])->name('payroll.rows');
    Route::get('payroll/{payroll}/slip-pdf', [PayrollController::class, 'slipPdf'])->name('payroll.slip.pdf');
    Route::patch('payroll/{payroll}/pay', [PayrollController::class, 'pay'])->name('payroll.pay');
    Route::patch('payroll/period/{period}/pay-all', [PayrollController::class, 'payAll'])->name('payroll.period.pay-all');
    Route::post('payroll/period/open', [PayrollController::class, 'openPeriod'])->name('payroll.period.open');
    Route::patch('payroll/period/{period}/close', [PayrollController::class, 'closePeriod'])->name('payroll.period.close');
    Route::patch('payroll/period/{period}/reopen', [PayrollController::class, 'reopenPeriod'])->name('payroll.period.reopen');
    Route::resource('payroll', PayrollController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);

    Route::patch('expenses/period/{period}/close', [ExpenseController::class, 'periodClose'])->name('expenses.period.close');
    Route::patch('expenses/period/{period}/reopen', [ExpenseController::class, 'periodReopen'])->name('expenses.period.reopen');
    Route::get('expenses/rows', [ExpenseController::class, 'rows'])->name('expenses.rows');
    Route::get('expenses/sync/changes', [ExpenseController::class, 'changes'])->name('expenses.sync.changes');
    Route::get('expenses/{expense}/attachment', [ExpenseController::class, 'attachment'])->name('expenses.attachment');
    Route::resource('expenses', ExpenseController::class);

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/detail/{invoice}', [ScheduleController::class, 'show'])->name('schedule.show');

    Route::get('/field-jobs/history', [FieldJobController::class, 'history'])->name('field-jobs.history');
    Route::get('/field-jobs', [FieldJobController::class, 'index'])->name('field-jobs.index');
    Route::get('/field-jobs/{fieldJob}', [FieldJobController::class, 'show'])->name('field-jobs.show');
    Route::put('/field-jobs/{fieldJob}/stages/{stage}/assignments', [FieldJobController::class, 'updateAssignments'])->name('field-jobs.stages.assignments');
    Route::patch('/field-jobs/{fieldJob}/stages/{stage}', [FieldJobController::class, 'updateStage'])->name('field-jobs.stages.update');
    Route::post('/field-jobs/{fieldJob}/stages/{stage}/photos', [FieldJobController::class, 'storePhotos'])->name('field-jobs.stages.photos.store');
    Route::get('/field-jobs/{fieldJob}/stages/{stage}/photos/{photo}', [FieldJobController::class, 'photo'])->name('field-jobs.stages.photos.show');
    Route::delete('/field-jobs/{fieldJob}/stages/{stage}/photos/{photo}', [FieldJobController::class, 'destroyPhoto'])->name('field-jobs.stages.photos.destroy');

    Route::get('/notifications/sync/changes', [NotificationController::class, 'changes'])->name('notifications.sync.changes');
    Route::get('/notifications/rows', [NotificationController::class, 'rows'])->name('notifications.rows');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/index/sync/changes', [NotificationController::class, 'indexChanges'])->name('notifications.index.sync.changes');
    Route::get('/notifications/index/rows', [NotificationController::class, 'indexRows'])->name('notifications.index.rows');
    Route::get('/notifications/preferences', [NotificationPreferenceController::class, 'index'])->name('notifications.preferences.index');
    Route::post('/notifications/preferences', [NotificationPreferenceController::class, 'store'])->name('notifications.preferences.store');

    Route::get('/users/export/pdf', [UserController::class, 'exportPdf'])->name('users.export.pdf');
    Route::get('/users/{user}/photo/{kind}', [UserController::class, 'photo'])->whereIn('kind', ['profile', 'ktp'])->name('users.photo');
    Route::patch('/users/{user}/activate', [UserController::class, 'activate'])->name('users.active');
    Route::patch('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactive');
    Route::resource('users', UserController::class);
    Route::get('/role-permissions', [RolePermissionController::class, 'index'])->name('role-permissions.index');
    Route::post('/role-permissions', [RolePermissionController::class, 'store'])->name('role-permissions.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
