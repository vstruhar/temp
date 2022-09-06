<?php

namespace App\Http\Controllers\Settings;

use App\Services\ImageService;
use Inertia\Inertia;
use App\Models\CompanyDetails;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceSettingsStoreRequest;

class InvoiceSettingsController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $companyDetails = auth()->user()->currentTeam->companyDetails;

        if ($companyDetails) {
            $this->authorize('view', $companyDetails);
        }

        return Inertia::render('Settings', ['section' => 'Invoice', 'data' => $companyDetails ?: null]);
    }

    /**
     * @param \App\Http\Requests\InvoiceSettingsStoreRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(InvoiceSettingsStoreRequest $request)
    {
        $this->authorize('create', CompanyDetails::class);

        $companyId = auth()->user()->currentTeam->id;

        $logoSignature = CompanyDetails::where('company_id', $companyId)->first();

        $logoPath      = ImageService::upload($request->file('logo'), $companyId, 'logo');
        $signaturePath = ImageService::upload($request->file('signature'), $companyId, 'signature');

        if ($request->file('logo')) {
            ImageService::resize($logoPath, 400, 400);
        }
        if ($request->file('signature')) {
            ImageService::resize($signaturePath, 400, 400);
        }

        CompanyDetails::updateOrCreate(
            ['company_id' => $companyId],
            [
                'logo'          => $request->file('logo') ? $logoPath : optional($logoSignature)->logo,
                'signature'     => $request->file('signature') ? $signaturePath : optional($logoSignature)->signature,
                'invoice_color' => $request->invoice_color,
            ]
        );

        return redirect()->route('settings.invoice')->banner(__('Images were saved successfully'));
    }

    /**
     * @param $type
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($type)
    {
        $companyDetails = CompanyDetails::where('company_id', auth()->user()->currentTeam->id)->first();

        $this->authorize('delete', $companyDetails);

        if ($type === 'logo') {
            unlink(storage_path("app/public/$companyDetails->logo"));
            $companyDetails->update(['logo' => null]);
        } else {
            unlink(storage_path("app/public/$companyDetails->signature"));
            $companyDetails->update(['signature' => null]);
        }

        return redirect()->route('settings.invoice')->banner(__('Image was deleted successfully'));
    }
}
