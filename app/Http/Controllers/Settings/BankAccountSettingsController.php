<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankAccountStoreRequest;
use App\Models\BankAccount;
use Inertia\Inertia;

class BankAccountSettingsController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->authorize('viewAny', BankAccount::class);

        $accounts = BankAccount::oldest()->get();

        return Inertia::render('Settings', ['section' => 'BankAccounts', 'data' => $accounts]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BankAccountStoreRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(BankAccountStoreRequest $request)
    {
        $this->authorize('create', BankAccount::class);

        collect($request->accounts)->each(function ($account) {
            if ($account['id'] === null) {
                $account['company_id'] = auth()->user()->currentTeam->id;
                BankAccount::create($account);
            } else {
                BankAccount::find($account['id'])->update($account);
            }
        });

        $count = BankAccount::where('company_id', auth()->user()->currentTeam->id)->count();

        if ($count === 1) {
            BankAccount::where('company_id', auth()->user()->currentTeam->id)
                       ->update(['default' => true]);
        }

        return redirect()->route('settings.bank_accounts')->banner(__('Bank accounts were saved successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        $this->authorize('delete', $bankAccount);

        $isDefault = $bankAccount->default;

        $bankAccount->delete();

        if ($isDefault) {
            BankAccount::where('company_id', auth()->user()->currentTeam->id)
                       ->limit(1)
                       ->update(['default' => true]);
        }

        return redirect()->route('settings.bank_accounts')->banner(__('Bank account was deleted successfully'));
    }
}
