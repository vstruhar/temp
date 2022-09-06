<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentNumberRequest;
use App\Models\DocumentNumber;
use App\Services\DocumentNumber\DocumentNumberGenerator;
use Inertia\Inertia;

class DocumentNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->authorize('viewAny', DocumentNumber::class);

        $documentNumbers = DocumentNumber::orderBy('name')
                                         ->get()
                                         ->map(function (DocumentNumber $item) {
                                             $nextNumber = (new DocumentNumberGenerator($item, now()))->getCounter() + 1;

                                             return array_merge($item->toArray(), ['current_counter' => $item->current_counter + $nextNumber]);
                                         });

        return Inertia::render('Settings', ['section' => 'DocumentNumbers', 'data' => $documentNumbers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\DocumentNumberRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentNumberRequest $request)
    {
        $this->authorize('create', DocumentNumber::class);

        $request->merge(['company_id' => $request->user()->currentTeam->id]);

        $documentNumber = DocumentNumber::create($request->all());
        $documentNumber->setDefault($request->is_default);

        return back()->banner(__('Invoice number was created successfully'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\DocumentNumberRequest $request
     * @param \App\Models\DocumentNumber               $invoiceNumber
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(DocumentNumberRequest $request, DocumentNumber $invoiceNumber)
    {
        $this->authorize('update', $invoiceNumber);

        $nextNumber = (new DocumentNumberGenerator($invoiceNumber, now()))->getCounter() + 1;

        $invoiceNumber->update(
            $request->merge(['current_counter' => $request->current_counter - $nextNumber])->all()
        );

        $invoiceNumber->setDefault($request->is_default);

        return back()->banner(__('Invoice number was successfully updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
