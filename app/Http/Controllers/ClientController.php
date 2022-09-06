<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Services\CountriesService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);

        $clients = Client::query()
                         ->where('company_id', auth()->user()->currentTeam->id)
                         ->when($request->filter, function (Builder $query) use ($request) {
                             $query->where('id', 'LIKE', "%{$request->filter}%")
                                   ->orWhere('name', 'LIKE', "%{$request->filter}%")
                                   ->orWhere('organization_id', 'LIKE', "%{$request->filter}%")
                                   ->orWhere('address', 'LIKE', "%{$request->filter}%");
                         })
                         ->when($request->field && $request->direction,
                             fn (Builder $query) => $query->orderBy($request->field, $request->direction),
                             fn (Builder $query) => $query->orderBy('name')
                         )
                         ->paginate(14);

        return Inertia::render('Clients', ['clients' => $clients]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->authorize('create', Client::class);

        $countryDropdownItems = CountriesService::dropdownOptions();

        return Inertia::render('Clients/Create', compact('countryDropdownItems'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ClientRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ClientRequest $request)
    {
        $this->authorize('create', Client::class);

        $companyId = $request->user()->currentTeam->id;

        $client = Client::create(
            array_merge(
                $request->all(),
                ['company_id' => $companyId]
            )
        );

        if ($request->expectsJson()) {
            return response()->json(['client' => $client]);
        }

        return redirect()->route('clients')->banner(__('Client was saved successfully'));
    }

    /**
     * @param  \App\Models\Client  $client
     *
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Client $client): Response
    {
        $this->authorize('view', $client);

        return Inertia::render('Clients/Show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     *
     * @return \Inertia\Response
     */
    public function edit(Client $client)
    {
        $this->authorize('update', $client);

        $countryDropdownItems = CountriesService::dropdownOptions();

        return Inertia::render('Clients/Edit', compact('client', 'countryDropdownItems'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ClientRequest  $request
     * @param  \App\Models\Client  $client
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);

        $client->update($request->all());

        if ($request->expectsJson()) {
            return response()->json(['client' => $client->fresh()]);
        }

        return redirect()->route('clients')->banner(__('Client was updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);

        $client->delete();

        return redirect()->back()->banner(__('Client was deleted successfully'));
    }
}
