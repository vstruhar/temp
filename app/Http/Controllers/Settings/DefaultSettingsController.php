<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultSettingsRequest;
use App\Models\BankAccount;
use App\Models\DefaultItem;
use App\Models\DocumentNumber;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DefaultSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $documentNumbersDropdownItems = DocumentNumber::dropdownSelect(null, ['type']);
        $documentNumbersDropdownItems = collect($documentNumbersDropdownItems)
            ->groupBy('type')
            ->reject(fn($items, $key) => empty($key))
            ->toArray();

        return Inertia::render('Settings', [
            'section' => 'DefaultSettings',
            'data'    => [
                'defaultBankAccount'           => $user->defaultBankAccount,
                'bankAccountsDropdownItems'    => BankAccount::dropdownOptions($defaultBankAccount->default_id ?? null),
                'defaultDocumentNumbers'       => $user->defaultDocumentNumbers,
                'documentNumbersDropdownItems' => $documentNumbersDropdownItems,
                'defaultIssuedBy'              => json_decode($user->defaultIssuedBy->value ?? '{}', false, 512, JSON_THROW_ON_ERROR),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $data = [
            'user_id'    => $user->id,
            'company_id' => $user->current_team_id,
        ];

        // clear
        DefaultItem::where($data)->delete();

        // bank account
        if ($request->bank_account) {
            DefaultItem::updateOrCreate($data + ['type' => DefaultItem::TYPE_BANK_ACCOUNT], ['default_id' => $request->bank_account]);
        }

        // document numbers
        collect(DocumentNumber::ALL_TYPES)
            ->filter(fn($type) => isset($request->document_numbers[$type]))
            ->each(static function ($type) use ($user, $request, $data) {
                $documentNumberIds = DocumentNumber::where('company_id', $user->current_team_id)
                                                   ->where('type', $type)
                                                   ->pluck('id');

                $defaultItem = DefaultItem::query()
                                          ->where([
                                              'user_id'    => $user->id,
                                              'company_id' => $user->current_team_id,
                                              'type'       => DefaultItem::TYPE_DOCUMENT_NUMBER,
                                          ])
                                          ->whereIn('default_id', $documentNumberIds)
                                          ->first();

                if ($defaultItem) {
                    $defaultItem->update(['default_id' => $request->document_numbers[$type]]);
                } else {
                    DefaultItem::create($data + ['type' => DefaultItem::TYPE_DOCUMENT_NUMBER, 'default_id' => $request->document_numbers[$type]]);
                }
            });

        // issued by
        DefaultItem::create($data + [
                'type'  => DefaultItem::TYPE_ISSUED_BY,
                'value' => json_encode($request->issued_by, JSON_THROW_ON_ERROR),
            ]);

        return back()->banner(__('Default settings where successfully updated'));
    }
}
