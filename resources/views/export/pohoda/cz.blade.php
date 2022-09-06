<dat:dataPack xmlns:dat="http://www.stormware.cz/schema/version_2/data.xsd" xmlns:inv="http://www.stormware.cz/schema/version_2/invoice.xsd" xmlns:typ="http://www.stormware.cz/schema/version_2/type.xsd" version="2.0" application="Ponfys24.sk" note="Vydane FA">
    @foreach($documents as $document)
    <dat:dataPackItem version="2.0" id="{{ $document->number }}">
        <inv:invoice version="2.0">
            <inv:invoiceHeader>
                <inv:invoiceType>issuedInvoice</inv:invoiceType>
                <inv:number>
                    <typ:numberRequested checkDuplicity="true">{{ $document->number }}</typ:numberRequested>
                </inv:number>
                <inv:symVar>{{ $document->number }}</inv:symVar>
                <inv:originalDocument>{{ $document->number }}</inv:originalDocument>
                <inv:numberOrder>{{ $document->order_number }}</inv:numberOrder>
                <inv:date>{{ optional($document->date_created)->format('Y-m-d') }}</inv:date>
                <inv:dateTax>{{ optional($document->date_created)->format('Y-m-d') }}</inv:dateTax>
                <inv:dateAccounting>{{ optional($document->date_created)->format('Y-m-d') }}</inv:dateAccounting>
                <inv:dateDue>{{ optional($document->billing_date)->format('Y-m-d') }}</inv:dateDue>
                <inv:text>{{ $document->name }}</inv:text>
                <inv:partnerIdentity>
                    <typ:address>
                        <typ:company>{{ data_get($document->company_details, 'name') }}</typ:company>
                        <typ:name/>
                        <typ:street>{{ data_get($document->company_details, 'address') }}</typ:street>
                        <typ:city>{{ data_get($document->company_details, 'city') }}</typ:city>
                        <typ:zip>{{ data_get($document->company_details, 'postal_code') }}</typ:zip>
                        <typ:ico>{{ data_get($document->company_details, 'organization_id') }}</typ:ico>
                        <typ:dic>{{ data_get($document->company_details, 'tax') }}</typ:dic>
                        <typ:icDph>{{ data_get($document->company_details, 'value_added_tax') }}</typ:icDph>
                        <typ:country>
                            <typ:ids>{{ data_get($document->company_details, 'country') }}</typ:ids>
                        </typ:country>
                        <typ:phone>{{ data_get($document->company_details, 'phone') }}</typ:phone>
                        <typ:fax>{{ data_get($document->company_details, 'fax') }}</typ:fax>
                        <typ:email>{{ data_get($document->company_details, 'email') }}</typ:email>
                    </typ:address>
                    <typ:shipToAddress>
                        <typ:company/>
                        <typ:street/>
                        <typ:city/>
                        <typ:zip/>
                    </typ:shipToAddress>
                </inv:partnerIdentity>
                <inv:symConst/>
                <inv:note>{{ $document->note }}</inv:note>
                <inv:intNote/>
                <inv:myIdentity>
                    <typ:address>
                        <typ:company>{{ data_get($document->client, 'name') }}</typ:company>
                        <typ:city>{{ data_get($document->client, 'city') }}</typ:city>
                        <typ:street>{{ data_get($document->client, 'address') }}</typ:street>
                        <typ:zip>{{ data_get($document->client, 'postal_code') }}</typ:zip>
                        <typ:ico>{{ data_get($document->client, 'organization_id') }}</typ:ico>
                        <typ:dic>{{ data_get($document->client, 'tax') }}</typ:dic>
                        <typ:phone>{{ data_get($document->client, 'phone') }}</typ:phone>
                        <typ:email>{{ data_get($document->client, 'email') }}</typ:email>
                    </typ:address>
                </inv:myIdentity>
            </inv:invoiceHeader>
            <inv:invoiceDetail>
                @foreach($document->items as $item)
                <inv:invoiceItem>
                    <inv:text>{{ $item->name }}</inv:text>
                    <inv:quantity>{{ $item->quantity }}</inv:quantity>
                    <inv:unit>{{ $item->unit }}</inv:unit>
                    <inv:payVAT>{{ $item->tax > 0 ? 'true' : 'false' }}</inv:payVAT>
                    <inv:rateVAT>{{ $item->tax > 0 ? $item->tax : 'none' }}</inv:rateVAT>
                    <inv:discountPercentage>{{ $item->discount_percent }}</inv:discountPercentage>
                    <inv:foreignCurrency>
                        <typ:unitPrice>{{ $item->price }}</typ:unitPrice>
                        <typ:price>{{ $item->price * $item->quantity }}</typ:price>
                        <typ:priceVAT>{{ $item->price_with_tax - ($item->price * $item->quantity) }}</typ:priceVAT>
                        <typ:priceSum>{{ $item->price_with_tax }}</typ:priceSum>
                    </inv:foreignCurrency>
                    <inv:note>{{ $item->description }}</inv:note>
                </inv:invoiceItem>
                @endforeach
            </inv:invoiceDetail>
        </inv:invoice>
    </dat:dataPackItem>
    @endforeach
</dat:dataPack>
