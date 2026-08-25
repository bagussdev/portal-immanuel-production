<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    private const FINANCIAL_ABILITIES = [
        'bankdetailmenu', 'createbankdetail', 'editbankdetail', 'deletebankdetail',
        'quotationmenu', 'createquotation', 'editquotation', 'deletequotation', 'approvequotation',
        'invoicemenu', 'createinvoice', 'editinvoice', 'deleteinvoice', 'issueinvoice',
        'adddp', 'voidpayment', 'voidinvoice', 'paymentsmenu',
        'expensesmenu', 'createexpenses', 'editexpenses', 'deleteexpenses', 'manageexpenses',
        'exportuserdata',
    ];

    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('isMaster', fn (User $user) => $user->isMaster());

        Gate::before(function (User $user, $ability) {
            if (! $user->active) {
                return false;
            }
            if (in_array($user->roleName(), ['mandor', 'user'], true)
                && in_array($ability, self::FINANCIAL_ABILITIES, true)) {
                return false;
            }
            if ($user->isMaster()) {
                return true;
            }

            return $user->hasPermission($ability) ? true : null;
        });
    }
}
