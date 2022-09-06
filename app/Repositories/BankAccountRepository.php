<?php

namespace App\Repositories;

use App\Models\BankAccount;
use App\Models\Team;

class BankAccountRepository
{
    /**
     * @param \App\Models\Team $company
     *
     * @return \App\Models\BankAccount[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getBankAccounts(Team $company)
    {
        $items = BankAccount::where('company_id', $company->id)
                            ->withoutGlobalScope('company_bank_accounts_only')
                            ->get()
                            ->map(static function (BankAccount $bankAccount) {
                                return [
                                    'default' => $bankAccount->usersDefault
                                        ? $bankAccount->usersDefault->default_id === $bankAccount->id
                                        : $bankAccount->default,
                                ] + $bankAccount->toArray();
                            });

        // if there is no default, use owners default
        if ($items->every('default', false)) {
            $defaultId = BankAccount::where('company_id', $company->id)
                                    ->where('default', true)
                                    ->withoutGlobalScope('company_bank_accounts_only')
                                    ->value('id');

            $items = $items->map(static function ($bankAccount) use ($defaultId) {
                return ['default' => $bankAccount['id'] === $defaultId] + $bankAccount;
            });
        }

        return $items;
    }
}
