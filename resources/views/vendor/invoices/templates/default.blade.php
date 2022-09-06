@php
$itemsHaveImage = collect($invoice->items)->some(fn ($item) => $item->image);
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
{{--        <title>{{ $invoice->name }}</title>--}}
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <style type="text/css" media="screen">
            html {
                font-family: sans-serif;
                line-height: 1.15;
                margin: 0;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                font-weight: 400;
                line-height: 1.5;
                color: #212529;
                text-align: left;
                background-color: #fff;
                font-size: 10px;
                margin: 36pt;
            }

            h4 {
                margin-top: 0;
                margin-bottom: 0.5rem;
            }

            p {
                margin-top: 0;
                /* margin-bottom: 1rem; */
            }

            strong {
                font-weight: bolder;
            }

            img {
                vertical-align: middle;
                border-style: none;
            }

            table {
                border-collapse: collapse;
            }

            th {
                text-align: inherit;
            }

            h4, .h4 {
                margin-bottom: 0.5rem;
                font-weight: 500;
                line-height: 1.2;
            }

            h4, .h4 {
                font-size: 1.5rem;
            }

            .table {
                width: 100%;
                /* margin-bottom: 1rem; */
                color: #212529;
            }

            .table th,
            .table td {
                padding: 0.75rem;
                vertical-align: top;
            }

            .table.table-items td {
                border-top: 1px solid {{ $invoice->lightColor(15) }};
            }

            .table.table-items tr.border-b td,
            .table.table-items tr td.border-b {
                border-bottom: 1px solid {{ $invoice->lightColor(15) }};
            }

            .table.table-items td.border-0 {
                border-top: none !important;
            }

            .table thead th {
                vertical-align: bottom;
                border-bottom: 2px solid {{ $invoice->lightColor(15) }};
            }

            .table thead.blue-line th {
                vertical-align: bottom;
                border-bottom: 3px solid {{ $invoice->color }};
            }

            .mb-0 {
                margin-bottom: 0 !important;
            }
            .mt-5 {
                margin-top: 3rem !important;
            }

            .pr-0,
            .px-0 {
                padding-right: 0 !important;
            }

            .p-0 {
                padding: 0 !important;
            }

            .pl-0,
            .px-0 {
                padding-left: 0 !important;
            }

            .nowrap {
                white-space: nowrap;
            }

            .bold {
                font-weight: bold;
            }

            .text-right {
                text-align: right !important;
            }

            .text-center {
                text-align: center !important;
            }

            .text-uppercase {
                text-transform: uppercase !important;
            }

            * {
                font-family: "DejaVu Sans";
            }

            body, h1, h2, h3, h4, h5, h6, table, th, tr, td, p, div {
                line-height: 1.1;
            }

            p {
                margin-bottom: 6px;
            }

            .company-name {
                font-size: 1.7rem;
                line-height: 1.45rem;
                font-weight: bold;
                color: {{ $invoice->color }};
                letter-spacing: 1px;
                margin-bottom: 10px;
            }

            .company-name.with-logo {
                font-size: 1.6rem;
                margin-top: 0;
                text-align: center;
            }

            img.logo {
                width: 250px;
                height: auto;
                margin: 0;
            }

            h2.invoice-name {
                font-size: 1.2rem;
                font-weight: bold;
                margin: 5px 0 5px 0;
            }

            .qr-code {
                width: 120px;
                height: auto;
            }

            .table.seller-buyer {
                margin-top: -15px;
            }

            .table.bank-accounts {
                background-color: {{ $invoice->lightColor(37) }};
                margin-bottom: 12px;
            }

            .table.bank-accounts td {
                padding: 10px 15px;
            }

            .table.bank-accounts td p {
                margin: 4px 0;
                padding: 0;
            }

            .table.bank-accounts td .bank-account {
                margin-bottom: 10px;
            }

            .table.bank-accounts td .bank-account:last-child {
                margin-bottom: 0 !important;
            }

            .table.credit-note-summary {
                margin-top: 20px;
                width: 50%;
                float: right;
            }

            .table.credit-note-summary tr th,
            .table.credit-note-summary tr td {
                padding: 3px;
                margin: 0;
                font-size: 0.6rem;
            }

            .seller-name,
            .buyer-name {
                font-size: 1rem;
                font-weight: bold;
                margin-bottom: 8px;
            }

            .buyer-delivery-address {
                font-size: 0.85rem;
                font-weight: bold;
                margin-bottom: 8px;
            }

            .party-header {
                font-size: 1.5rem;
                font-weight: 400;
            }

            .item-description {
                margin-top: 5px;
                font-size: 9px;
            }

            .total-amount {
                font-size: 13px;
                font-weight: 700;
            }

            .item-image {
                padding: 8px 0;
                width: 100px;
                height: 100px;
            }

            .border-0 {
                border: none !important;
            }

            .cool-gray {
                color: #6B7280;
            }

            .no-wrap {
                white-space: nowrap;
            }

            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 50px;
                width: 100%;
                color: #888;
            }
        </style>
    </head>

    <body>
        {{-- Header --}}
        <table class="table first">
            <tbody>
            <tr>
                <td class="border-0 pl-0" style="width: 250px;">
                    @if($invoice->logo)
                        <img src="{{ $invoice->getLogo() }}" class="logo">
                    @endif
                    <h1 class="company-name {{ $invoice->logo ? 'with-logo' : '' }}">{{ $invoice->seller->name }}</h1>
                </td>

                <td class="border-0 pl-0 text-right">
                    <h2 class="invoice-name">{{ $invoice->name }}</h2>

                    <p>{{ __('Documents number') }}: <strong>{{ $invoice->invoiceNumber }}</strong></p>

                    <p>{{ __('Date of issue') }}: <strong>{{ $invoice->getDate() }}</strong></p>

                    @if ($invoice->dateAdded)
                        <p> {{ __('Delivery date') }}: <strong>{{ $invoice->dateAdded }}</strong></p>
                    @endif

                    @if ($invoice->billingDate)
                        <p> {{ __('Due by') }}: <strong>{{ $invoice->billingDate }}</strong></p>
                    @endif
                </td>

                @if ($invoice->qrCode)
                <td class="border-0" style="width: 120px;">
                    <img src="{{ $invoice->qrCode }}" class="qr-code">
                </td>
                @endif
            </tr>
            </tbody>
        </table>

        <table class="table seller-buyer">
            <thead class="blue-line">
            <tr>
                @if(data_get($invoice->buyer->shipping_address, 'address'))
                    <th class="border-0 pl-0 party-header" width="40%">{{ __('Seller') }}</th>
                @else
                    <th class="border-0 pl-0 party-header" width="50%">{{ __('Seller') }}</th>
                @endif
                <th class="border-0 pl-0 party-header">{{ __('Buyer') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                {{-- Seller --}}
                <td class="px-0">
                    @if($invoice->seller->name)
                        <p class="seller-name">{{ $invoice->seller->name }}</p>
                    @endif

                    @if($invoice->seller->address)
                        <p class="seller-address">
                            {{ __('Address') }}: {{ $invoice->seller->address }}
                        </p>
                    @endif

                    @if($invoice->seller->phone)
                        <p class="seller-phone">
                            {{ __('Phone') }}: {{ $invoice->seller->phone }}
                        </p>
                    @endif

                    @foreach($invoice->seller->custom_fields as $key => $value)
                        <p class="seller-custom-field">
                            @if ($value != null)
                                {{ ucfirst($key) }}: {{ $value }}
                            @endif
                        </p>
                    @endforeach
                </td>

                {{-- Buyer --}}
                <td class="px-0">
                    <table>
                        <tr>
                            <td width="50%">
                                @if($invoice->buyer->name)
                                    <p class="buyer-name">{{ $invoice->buyer->name }}</p>
                                @endif

                                @if($invoice->buyer->address)
                                    <p class="buyer-address">
                                        {{ __('Address') }}: {{ $invoice->buyer->address }}
                                    </p>
                                @endif

                                @if($invoice->buyer->phone)
                                    <p class="buyer-phone">
                                        {{ __('Phone') }}: {{ $invoice->buyer->phone }}
                                    </p>
                                @endif

                                @foreach($invoice->buyer->custom_fields as $key => $value)
                                    @if ($value != null)
                                        <p class="buyer-custom-field">
                                            {{ ucfirst($key) }}: {{ $value }}
                                        </p>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                @if(data_get($invoice->buyer->shipping_address, 'address'))
                                    <p class="buyer-delivery-address">{{ __('Delivery address') }}</p>

                                    <p class="buyer-address">
                                        {{ data_get($invoice->buyer->shipping_address, 'name') }}
                                    </p>

                                    @if(data_get($invoice->buyer->shipping_address, 'address'))
                                        <p class="buyer-address">
                                            {{ __('Address') }}:
                                            {{ data_get($invoice->buyer->shipping_address, 'address') }}
                                            {{ data_get($invoice->buyer->shipping_address, 'postal_code') }}
                                            {{ data_get($invoice->buyer->shipping_address, 'city') }}

                                            @if(data_get($invoice->buyer->shipping_address, 'country'))
                                                {{ \App\Services\CountriesService::getName(data_get($invoice->buyer->shipping_address, 'country')) }}
                                            @endif
                                        </p>
                                    @endif

                                    @if(data_get($invoice->buyer->shipping_address, 'phone'))
                                        <p class="buyer-phone">
                                            {{ __('Phone') }}: {{ data_get($invoice->buyer->shipping_address, 'phone') }}
                                        </p>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>

        <table class="table bank-accounts">
            <tr>
                <td width="50%">
                    @foreach($invoice->bankAccounts as $bankAccount)
                        <div class="bank-account {{ $bankAccount->default ? 'bold' : '' }}">
                            <p>{{ $bankAccount->name }}: {{ $bankAccount->number_account }}</p>
                            <p>IBAN / SWIFT: {{ $bankAccount->iban }} / {{ $bankAccount->swift }}</p>
                        </div>
                    @endforeach
                </td>
                <td>
                    @if ($invoice->variableSymbol)
                        <p>{{ __('Variable code') }}: {{ $invoice->variableSymbol }}</p>
                    @endif

                    @if ($invoice->paymentMethod)
                        <p>{{ __('Payment method') }}: {{ __($invoice->getPaymentMethod()) }}</p>
                    @endif
                </td>
            </tr>
        </table>

        {{-- note before items --}}
        @if ($invoice->noteBeforeItems)
            <span>
             <p style="margin-left: 10px;">{{ $invoice->noteBeforeItems }}</p>
         </span>
        @endif

        {{-- Table --}}
        <table class="table table-items">
            <tr>
                @if($itemsHaveImage)
                    <th scope="col"></th>
                @endif

                <th scope="col" class="border-0 pl-0">{{ __('Description') }}</th>

                <th scope="col" class="text-center border-0">{{ __('Unit') }}</th>

                <th scope="col" class="text-center border-0">{{ __('Quantity') }}</th>

                @if($invoice->taxable)
                    <th scope="col" class="text-right border-0">{{ __('Price (without VAT)') }}</th>
                @else
                    <th scope="col" class="text-right border-0">{{ __('Unit price') }}</th>
                @endif

                @if($invoice->hasItemDiscount)
                    <th scope="col" class="text-right border-0">{{ __('Discount') }}</th>
                @endif

                @if($invoice->taxable)
                    <th scope="col" class="text-right border-0 no-wrap">{{ __('Tax') }} (%)</th>
                @endif

                @if($invoice->taxable)
                    <th scope="col" class="text-right border-0 pr-0">{{ __('Sub total') }}</th>
                @else
                    <th scope="col" class="text-right border-0 pr-0">{{ __('Total') }}</th>
                @endif
            </tr>

            {{-- Items --}}
            @foreach($invoice->items as $item)
                @if($loop->last)
                <tr class="border-b">
                @else
                <tr>
                @endif
                    @if($itemsHaveImage)
                    <td class="p-0" style="width: 104px;">
                        @if($item->image)
                            <img src="{{ $item->image }}" class="item-image">
                        @endif
                    </td>
                    @endif

                    <td class="pl-0">
                        {{ $item->title }}

                        @if($item->description)
                            <p class="cool-gray item-description">{{ $item->description }}</p>
                        @endif
                    </td>

                    <td class="text-center">{{ __($item->units) }}</td>

                    <td class="text-center">{{ $item->quantity }}</td>

                    <td class="text-right nowrap">
                        {{ $invoice->formatCurrency($item->price_per_unit) }}
                    </td>

                    @if($invoice->hasItemDiscount)
                        <td class="text-right nowrap">
                            {{ $invoice->formatCurrency($item->discount) }}
                        </td>
                    @endif

                    @if($invoice->taxable)
                    <td class="text-right">{{ $item->tax_percent_value }}</td>
                    <td class="text-right pr-0 nowrap">
                        {{ $invoice->formatCurrency($item->calculated_sub_total_price) }}
                    </td>
                    @else
                    <td class="text-right pr-0 nowrap">
                        {{ $invoice->formatCurrency(($item->price_per_unit * $item->quantity) - $item->discount) }}
                    </td>
                    @endif
                </tr>
            @endforeach

            {{-- Summary --}}
            @php
                $colspan = 3;

                if ($invoice->taxable) $colspan += 1;
                if ($invoice->hasItemOrInvoiceDiscount()) $colspan += 1;
                if ($itemsHaveImage) $colspan += 1;
            @endphp

            {{--
            @if($invoice->hasItemOrInvoiceDiscount())
                <tr>
                    <td class="text-right pl-0" colspan="{{ $colspan }}">{{ __('Total discount') }}</td>
                    <td class="text-right pr-0 nowrap">
                        {{ $invoice->formatCurrency($invoice->total_discount) }}
                    </td>
                </tr>
            @endif
            --}}

            @php
                $subtotalOfItemsWithTaxZero = $invoice->taxable ? $invoice->getSubtotalOfItemsWithZeroTax() : 0;
            @endphp

            @if($invoice->taxable)
                <tr>
                    <td class="border-0" colspan="{{ $colspan }}"></td>
                    <td class="text-right pl-0 nowrap">{{ __('Taxable amount') }}</td>
                    <td class="text-right pr-0 nowrap">
                        {{ $invoice->formatCurrency($invoice->taxable_amount) }}
                    </td>
                </tr>
            @endif

            @if($invoice->taxable)
                <tr>
                    <td class="border-0" colspan="{{ $colspan }}"></td>
                    <td class="text-right pl-0 nowrap">{{ __('Total taxes') }}</td>
                    <td class="text-right pr-0 nowrap">
                        {{ $invoice->formatCurrency($invoice->amount_with_tax - $invoice->amount - $subtotalOfItemsWithTaxZero) }}
                    </td>
                </tr>
            @endif

            @if($invoice->taxable && $invoice->hasItemsWithZeroTax())
                <tr>
                    <td class="border-0" colspan="{{ $colspan }}"></td>
                    <td class="text-right pl-0 nowrap">{{ __('Without tax') }}</td>
                    <td class="text-right pr-0 nowrap">
                        {{ $invoice->formatCurrency($invoice->getSubtotalOfItemsWithZeroTax()) }}
                    </td>
                </tr>
            @endif

            <tr>
                <td class="border-0" colspan="{{ $colspan }}"></td>
                <td class="text-right pl-0 total-amount nowrap border-b">{{ __('Total amount') }}</td>
                <td class="text-right pr-0 total-amount nowrap border-b">
                    {{ $invoice->formatCurrency($invoice->taxable ? $invoice->amount_with_tax : $invoice->amount) }}
                </td>
            </tr>
        </table>

        @if($invoice->notes)
            <p style="margin-bottom: 15px;">
                {{ __('Notes') }}: {!! $invoice->notes !!}
            </p>
        @endif

        {{-- Credit Note Summary --}}
        @if ($invoice->model->isCreditNote())
            <table class="table credit-note-summary">
                <tr>
                    <th scope="col" class="text-center"></th>
                    <th scope="col" class="text-center">{{ __('Originally') }}</th>
                    <th scope="col" class="text-center">{{ __('Correction') }}</th>
                    <th scope="col" class="text-center">{{ __('Corrected') }}</th>
                </tr>
                </thead>

                @if($invoice->taxable)
                <tbody>
                <tr>
                    <td class="text-right bold">{{ __('VAT Base') }}</td>
                    <td class="text-center nowrap">{{ $invoice->formatCurrency($invoice->model->amount) }}</td>
                    <td class="text-center nowrap">{{ $invoice->formatCurrency($invoice->model->invoice->amount) }}</td>
                    <td class="text-center nowrap">{{ $invoice->formatCurrency($invoice->model->amount + $invoice->model->invoice->amount) }}</td>
                </tr>
                <tr>
                    <td class="text-right bold">{{ __('Tax') }}</td>
                    <td class="text-center nowrap">{{ $invoice->formatCurrency($invoice->model->invoice->amount_with_tax - $invoice->model->invoice->amount) }}</td>
                    <td class="text-center nowrap">{{ $invoice->formatCurrency($invoice->model->amount_with_tax - $invoice->model->amount) }}</td>
                    <td class="text-center nowrap">
                        {{ $invoice->formatCurrency(($invoice->model->invoice->amount_with_tax - $invoice->model->invoice->amount) + ($invoice->model->amount_with_tax - $invoice->model->amount)) }}
                    </td>
                </tr>
                <tr>
                    <td class="text-right bold">{{ __('Total') }}</td>
                    <td class="text-center nowrap">{{ $invoice->formatCurrency($invoice->model->invoice->amount + ($invoice->model->invoice->amount_with_tax - $invoice->model->invoice->amount)) }}</td>
                    <td class="text-center nowrap">{{ $invoice->formatCurrency($invoice->model->amount + ($invoice->model->amount_with_tax - $invoice->model->amount)) }}</td>
                    <td class="text-center nowrap">
                        {{ $invoice->formatCurrency(($invoice->model->invoice->amount + ($invoice->model->invoice->amount_with_tax - $invoice->model->invoice->amount)) + ($invoice->model->amount + ($invoice->model->amount_with_tax - $invoice->model->amount))) }}
                    </td>
                </tr>
                @else
                    <tr>
                        <td class="text-right bold">{{ __('Total') }}</td>
                        <td class="text-center nowrap">{{ $invoice->formatCurrency($invoice->model->invoice->amount) }}</td>
                        <td class="text-center nowrap">{{ $invoice->formatCurrency($invoice->model->amount) }}</td>
                        <td class="text-center nowrap">
                            {{ $invoice->formatCurrency($invoice->model->invoice->amount + $invoice->model->amount) }}
                        </td>
                    </tr>
                @endif
            </table>
        @endif

        <div style="margin:0;padding:0">
            <p class="mb-1">{{ __('Signature and stamp') }}:</p>

            @if($invoice->signatureFile)
                <img src="{{ $invoice->signatureFile }}" style="margin:0;padding:0; max-width: 160px; max-height: 90px;">
            @endif
        </div>

        <table class="footer">
            <tr>
                @if($invoice->invoiceIssued)
                    <td style="padding-left: 40px;">{{ __('Issued by') }}: {{ $invoice->invoiceIssued }}</td>
                @endif

                @if($invoice->issuedByPhone)
                    <td class="text-center">{{ __('Phone') }}: {{ $invoice->issuedByPhone }}</td>
                @endif

                @if($invoice->issuedByEmail)
                    <td style="padding-right: 40px;" class="text-right">{{ __('Email') }}: {{ $invoice->issuedByEmail }}</td>
                @endif
            </tr>
        </table>

        <script type="text/php">
            if (isset($pdf) && $PAGE_COUNT > 1) {
                $text = "{PAGE_NUM} / {PAGE_COUNT}";
                $size = 8;
                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 3;
                $x = ($pdf->get_width() - $width) - 10;
                $y = $pdf->get_height() - 40;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>
    </body>
</html>
