<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <meta http-equiv="Cache-control" content="no-cache">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <style type="text/css">
            * {
                font-family: DejaVu Sans;
            }

            body, th, td, h5 {
                font-size: 12px;
                color: #000;
            }

            .container {
                padding: 20px;
                display: block;
            }

            .invoice-summary {
                margin-bottom: 20px;
            }

            .table {
                margin-top: 20px;
            }

            .table table {
                width: 100%;
                border-collapse: collapse;
                text-align: left;
            }

            .table thead th {
                font-weight: 700;
                border-top: solid 1px #d3d3d3;
                border-bottom: solid 1px #d3d3d3;
                border-left: solid 1px #d3d3d3;
                padding: 5px 10px;
                background: #F4F4F4;
            }

            .table thead th:last-child {
                border-right: solid 1px #d3d3d3;
            }

            .table tbody td {
                padding: 5px 10px;
                border-bottom: solid 1px #d3d3d3;
                border-left: solid 1px #d3d3d3;
                color: #3A3A3A;
                vertical-align: middle;
            }

            .table tbody td p {
                margin: 0;
            }

            .table tbody td:last-child {
                border-right: solid 1px #d3d3d3;
            }

           .sale-summary {
                margin-top: 40px;
                float: right;
            }

            .sale-summary tr td {
                padding: 3px 5px;
            }

            .sale-summary tr.bold {
                font-weight: 600;
            }

            .label {
                color: #000;
                font-weight: bold;
            }

            .logo {
                height: 70px;
                width: 70px;
            }

            .merchant-details {
                margin-bottom: 5px;
            }

            .merchant-details-title {
                font-weight: bold;
            }

            .text-center {
                text-align: center;
            }
        </style>
    </head>

    <body style="background-image: none; background-color: #fff;">
        <div class="container">
            <div class="header">
                <div class="row">
                    <div class="col-12">
                        <h1 class="text-center">{{ __('admin::app.sales.invoices.invoice') }}</h1>
                    </div>
                </div>
                @if (core()->getConfigData('sales.orderSettings.invoice_slip_design.logo'))
                    <div class="image">
                        <img class="logo" src="{{ Storage::url(core()->getConfigData('sales.orderSettings.invoice_slip_design.logo')) }}"/>
                    </div>
                @endif
                <div class="merchant-details">
                    <div><span class="merchant-details-title">{{ core()->getConfigData('sales.shipping.origin.store_name') ? core()->getConfigData('sales.shipping.origin.store_name') : '' }}</span></div>
                    <div>{{ core()->getConfigData('sales.shipping.origin.address1') ? core()->getConfigData('sales.shipping.origin.address1') : '' }}</div>
                    <div>
                        <span>{{ core()->getConfigData('sales.shipping.origin.zipcode') ? core()->getConfigData('sales.shipping.origin.zipcode') : '' }}</span>
                        <span>{{ core()->getConfigData('sales.shipping.origin.city') ? core()->getConfigData('sales.shipping.origin.city') : '' }}</span></div>
                    <div>{{ core()->getConfigData('sales.shipping.origin.state') ? core()->getConfigData('sales.shipping.origin.state') : '' }}</div>
                    <div>{{ core()->getConfigData('sales.shipping.origin.country') ?  core()->country_name(core()->getConfigData('sales.shipping.origin.country')) : '' }}</div>
                </div>
                <div class="merchant-details">
                    @if (core()->getConfigData('sales.shipping.origin.contact'))
                        <div><span class="merchant-details-title">{{ __('admin::app.admin.system.contact-number') }}:</span> {{ core()->getConfigData('sales.shipping.origin.contact') }}</div>
                    @endif
                    @if (core()->getConfigData('sales.shipping.origin.vat_number'))
                        <div><span class="merchant-details-title">{{ __('admin::app.admin.system.vat-number') }}:</span> {{ core()->getConfigData('sales.shipping.origin.vat_number') }}</div>
                    @endif
                    @if (core()->getConfigData('sales.shipping.origin.bank_details'))
                        <div><span class="merchant-details-title">{{ __('admin::app.admin.system.bank-details') }}:</span> {{ core()->getConfigData('sales.shipping.origin.bank_details') }}</div>
                    @endif
                </div>
            </div>

            <div class="invoice-summary">
                <div class="row">
                    <span class="label">{{ __('admin::app.sales.order.id') }} -</span>
                    <span class="value">#{{ $order->id }}</span>
                </div>

                <div class="row">
                    <span class="label">{{ __('admin::app.sales.order.id') }} -</span>
                    <span class="value">#{{ $order->id }}</span>
                </div>

                <div class="row">
                    <span class="label">{{ __('admin::app.sales.invoices.order-date') }} </span>
                    <span class="value">{{ $order->created_at->format('d-m-Y') }}</span>
                </div>

                <div class="table address">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50%">{{ __('admin::app.sales.invoices.bill-to') }}</th>
                                @if ($order->shipping_address)
                                    <th>{{ __('admin::app.sales.invoices.ship-to') }}</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                @if ($order->billing_address)
                                    <td>
                                        <p>{{ $order->billing_address->company_name ?? '' }}</p>
                                        <p>{{ $order->billing_address->name }}</p>
                                        <p>{{ $order->billing_address->address1 }}</p>
                                        <p>{{ $order->billing_address->postcode . ' ' .$order->billing_address->city }} </p>
                                        <p>{{ $order->billing_address->state }}</p>
                                        <p>{{ core()->country_name($order->billing_address->country) }}</p>
                                        {{ __('shop::app.checkout.onepage.contact') }} : {{ $order->billing_address->phone }}
                                    </td>
                                @endif

                                @if ($order->shipping_address)
                                    <td>
                                        <p>{{ $order->shipping_address->company_name ?? '' }}</p>
                                        <p>{{ $order->shipping_address->name }}</p>
                                        <p>{{ $order->shipping_address->address1 }}</p>
                                        <p>{{ $order->shipping_address->postcode . ' ' . $order->shipping_address->city }}</p>
                                        <p>{{ $order->shipping_address->state }}</p>
                                        <p>{{ core()->country_name($order->shipping_address->country) }}</p>
                                        {{ __('shop::app.checkout.onepage.contact') }} : {{ $order->shipping_address->phone }}
                                    </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table payment-shipment">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50%">{{ __('admin::app.sales.orders.payment-method') }}</th>

                                @if ($order->shipping_address)
                                    <th>{{ __('admin::app.sales.orders.shipping-method') }}</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    {{ core()->getConfigData('sales.paymentmethods.' . $order->payment->method . '.title') }}

                                    @php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($order->payment->method); @endphp

                                    @if (! empty($additionalDetails))
                                        <div>
                                            <label class="label">{{ $additionalDetails['title'] }}:</label>
                                            <p class="value">{{ $additionalDetails['value'] }}</p>
                                        </div>
                                    @endif
                                </td>

                                @if ($order->shipping_address)
                                    <td>
                                        {{ $order->shipping_title }}
                                    </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table items">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('admin::app.sales.orders.SKU') }}</th>
                                <th>{{ __('admin::app.sales.orders.product-name') }}</th>
                                <th class="text-center">{{ __('admin::app.sales.orders.price') }}</th>
                                <th class="text-center">{{ __('admin::app.sales.orders.qty') }}</th>
                                <th class="text-center">{{ __('admin::app.sales.orders.subtotal') }}</th>
                                <th class="text-center">{{ __('admin::app.sales.orders.tax-amount') }}</th>
                                <th class="text-center">{{ __('admin::app.sales.orders.grand-total') }}</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->getTypeInstance()->getOrderedItem($item)->sku }}</td>
                                    <td>
                                        {{ $item->name }}

                                        @if (isset($item->additional['attributes']))
                                            <div class="item-options">

                                                @foreach ($item->additional['attributes'] as $attribute)
                                                    <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}</br>
                                                @endforeach

                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ core()->formatBasePrice($item->base_price) }}</td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-center">{{ core()->formatBasePrice($item->base_total) }}</td>
                                    <td class="text-center">{{ core()->formatBasePrice($item->base_tax_amount) }}</td>
                                    <td class="text-center">{{ core()->formatBasePrice($item->base_total + $item->base_tax_amount) }}</td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>


                <table class="sale-summary">
                    <tr>
                        <td>{{ __('admin::app.sales.orders.subtotal') }}</td>
                        <td>-</td>
                        <td>{{ core()->formatBasePrice($order->base_sub_total) }}</td>
                    </tr>

                    <tr>
                        <td>{{ __('admin::app.sales.orders.shipping-handling') }}</td>
                        <td>-</td>
                        <td>{{ core()->formatBasePrice($order->base_shipping_amount) }}</td>
                    </tr>

                    <tr>
                        <td>{{ __('admin::app.sales.orders.tax') }}</td>
                        <td>-</td>
                        <td>{{ core()->formatBasePrice($order->base_tax_amount) }}</td>
                    </tr>

                    <tr>
                        <td>{{ __('admin::app.sales.orders.discount') }}</td>
                        <td>-</td>
                        <td>{{ core()->formatBasePrice($order->base_discount_amount) }}</td>
                    </tr>

                    <tr>
                        <td><strong>{{ __('admin::app.sales.orders.grand-total') }}</strong></td>
                        <td><strong>-</strong></td>
                        <td><strong>{{ core()->formatBasePrice($order->base_grand_total) }}</strong></td>
                    </tr>
                </table>

            </div>

        </div>
    </body>
</html>
