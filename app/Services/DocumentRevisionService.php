<?php

namespace App\Services;

use App\Casts\Currency;
use App\Casts\Decimal;
use App\Models\Document;
use App\Models\DocumentItem;
use App\Models\Revision;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DocumentRevisionService
{
    /**
     * @param \App\Models\Document $invoice
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function save(Document $invoice): Model
    {
        $invoice = $invoice->withoutRelations()->load('items');

        $invoiceRevisions = Revision::select('id')
                                    ->where('revisionable_id', $invoice->id)
                                    ->where('revisionable_type', Document::class)
                                    ->oldest()
                                    ->get();

        if ($invoiceRevisions->count() >= 5) {
            $invoiceRevisions->first()->delete();
        }

        return $invoice->revisions()
                       ->create([
                           'user_id' => auth()->user()->id,
                           'data'    => $invoice,
                       ]);
    }

    /**
     * @param \App\Models\Document $invoice
     * @param \App\Models\Revision $revision
     *
     * @return bool
     */
    public static function restore(Document $invoice, Revision $revision): bool
    {
        $restored = $invoice->update(
            collect($revision->data)->except('items')->toArray()
        );

        $currency = new Currency();
        $decimal  = new Decimal();

        foreach (data_get($revision, 'data.items', []) as $item) {
            DocumentItem::where('id', $item['id'])
                        ->update(
                            array_merge($item, [
                                'price'            => $currency->setValue($item['price']),
                                'price_with_tax'   => $currency->setValue($item['price_with_tax']),
                                'discount_amount'  => $currency->setValue($item['discount_amount']),
                                'discount_percent' => $currency->setValue($item['discount_percent']),
                                'quantity'         => $decimal->setValue($item['quantity']),
                                'created_at'       => Carbon::parse($item['created_at']),
                                'updated_at'       => Carbon::parse($item['updated_at']),
                            ])
                        );
        }

        $marked = $revision->update(['restored_at' => now()]);

        return $restored && $marked;
    }
}
