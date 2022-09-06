<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;
use App\Http\Requests\DuplicateDocumentRequest;
use App\Models\Client;
use App\Models\Document;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class DuplicateDocumentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param string                                      $type
     * @param \App\Http\Requests\DuplicateDocumentRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function store(string $type, DuplicateDocumentRequest $request)
    {
        $document = Document::with('client')->find($request->document_id);

        $this->authorize('duplicate', $document);

        $clientId = $request->client_id;

        if ($request->company_id !== auth()->user()->current_team_id) {
            DB::transaction(function () use ($document, $request, &$clientId) {
                // client
                if ($request->copy_client === true) {
                    $client = Client::where('company_id', $request->company_id)
                                    ->where('organization_id', $document->client['organization_id'])
                                    ->first();

                    if (!$client) {
                        $client             = Client::find($document->client_id)->replicate();
                        $client->company_id = $request->company_id;
                        $client->created_at = now();
                        $client->save();
                    }
                    $clientId = $client->id;
                }
            });

            // switch to company where document was created
            auth()->user()->switchTeam(Team::find($request->company_id));
        }

        return redirect()->route('documents.create', ['type' => $type, 'document' => $document->id, 'client' => $clientId]);
    }
}
