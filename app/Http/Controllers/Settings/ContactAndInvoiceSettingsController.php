<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyDetailsRequest;
use App\Models\CompanyDetails;
use App\Services\CountriesService;
use App\Services\PonfysAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;


class ContactAndInvoiceSettingsController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $companyDetails       = auth()->user()->currentTeam->companyDetails;
        $countryDropdownItems = CountriesService::dropdownOptions();

        if ($companyDetails) {
            $this->authorize('view', $companyDetails);
        }

        return Inertia::render('Settings', [
            'section' => 'ContactAndInvoice',
            'data'    => compact('countryDropdownItems', 'companyDetails'),
        ]);
    }

    /**
     * @param \App\Http\Requests\CompanyDetailsRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(CompanyDetailsRequest $request)
    {
        $companyDetails = CompanyDetails::where('company_id', $request->user()->currentTeam->id)->first();

        if ($companyDetails) {
            $this->authorize('update', $companyDetails);

            $data = $request->all();

            if ($request->tax_profile === 0) {
                $data['default_tax_percentage'] = 0;
            }

            $companyDetails->update($data);
        } else {
            $companyDetails = CompanyDetails::create(
                array_merge(
                    $request->all(),
                    ['company_id' => $request->user()->currentTeam->id]
                )
            );
        }

        // update company name
        auth()->user()->currentTeam->update(['name' => $request->name]);

        PonfysAdminService::updateCompany($companyDetails);

        return redirect()->back()->banner(__('Contact and invoice data was saved successfully'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTaxSettings(Request $request): JsonResponse
    {
        $request->validate(['default_tax_percentage' => 'required|numeric|min:0|max:99'], [], ['default_tax_percentage' => __('Tax')]);

        auth()->user()
            ->currentTeam
            ->companyDetails
            ->update(['default_tax_percentage' => $request->default_tax_percentage]);

        return response()->json();
    }
}
