<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

<head>
    <meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style type="text/css">
        * {
            font-family: DejaVu Sans;
        }

        body,
        th,
        td,
        h5 {
            font-size: 12px;
            color: #000;
        }

        .container {
            padding-left: 10%;
            padding-right: 10%;
            display: block;
        }

        .cover-row-left {
            flex-direction: row
        }

        .img_logo {
            float: left;
        }

        .invoice-summary {
            margin-bottom: 10px;
        }

        .right-header {
            display: flex;
            flex-direction: column;
        }

        .table {
            margin-top: 30px;
        }

        .table table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-top: 20px;
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

        .header-top {
            margin-bottom: 50px;
        }

        .label {
            color: #000;
            font-weight: bold;
            font-size: 18px;
        }

        .label-left {
            color: #000;
            font-weight: bold;
            font-size: 18px;
            text-decoration: underline;
        }

        .detail-row {
            color: #000;
            font-size: 18px;
        }

        .header-row {
            color: #000;
            font-size: 18px;
            text-align: center;
        }
        .detail-row-center {
            color: #000;
            font-size: 18px;
            text-align: center;
        }
        .row{
            margin-left: 50px;
        }

        .cover-row {
            margin-bottom: 30px;
            clear: left;
        }

        .logo {
            height: 200px;
            width: 200px;
        }

        .barcode {
            height: 80px;
            width: 300px;
            margin-bottom: 10px;
        }

        .row-header {
            text-align: center;
            color: #000;
            font-weight: bold;
            font-size: 18px;
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
                    <h1 class="text-center">Thông tin vận đơn </h1>
                </div>
            </div>
            <div class="header-top">
                <div class="cover-row">
                    <div class="cover-row-left">
                        <div class="img_logo">
                            <img class="logo"
                                src="https://file.hstatic.net/1000304191/file/logo_link_web_8abbc7d0ef2b4eab92b36eb7e4fae789_grande.png" />
                        </div>

                    </div>
                </div>
                <div class="invoice-summary">
                    <span class="header-row">Mã hóa đơn: {{ $order->id }}</span>
                </div>
                <div class="right-header">
                    <img class="barcode"
                        src="https://i.pinimg.com/564x/2a/23/57/2a2357905ec84a1082d2af5d863d490f.jpg" />
                </div>
                <div class="invoice-summary">
                    <div class="invoice-summary">
                        <span class="header-row">Đơn vị giao hàng: Giao hàng nhanh</span>
                    </div>
                    <div class="invoice-summary">
                        <span class="header-row">{{ __('admin::app.sales.invoices.order-date') }} :
                            {{ $order->created_at->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="cover-row">
                <div class="cover-row-left">
                    <span class="label-left">Người gửi:</span>
                    <span class="label">MMOutfit - 0888121868</span>
                </div>
                <div class="cover-row-left">
                    <span class="detail-row">352 Giải Phóng, Phường Phương Liệt, Quận Thanh Xuân, Hà Nội</span>
                </div>
            </div>

            <div class="cover-row">
                <div class="cover-row-left">
                    <span class="label-left">Người nhận:</span>
                    <span class="label"> {{ $order->billing_address->name }} -
                        {{ $order->shipping_address->phone }}</span>
                </div>
                <div class="cover-row-left">
                    <span class="detail-row">{{ $order->shipping_address->address1 }}
                        {{ $order->shipping_address->city }}</span>
                </div>
            </div>
            <div class="cover-row">
                <div class="cover-row-left">
                    <span class="label-left">Tổng thu người nhận:</span>
                    <span class="label"> {{ core()->formatBasePrice($order->base_grand_total) }}</span>
                </div>
            </div>

            <div class="cover-row">
                <div class="cover-row-left">
                    <span class="label-left">Lưu ý khi giao hàng:</span>
                    @if ($comment != null)
                        <span class="label"> {{ $comment->cmd_detail }}</span>
                    @endif
                </div>
            </div>
            <div class="cover-row">
                <div class="cover-row-left">
                    <span class="label-left">Thông tin đơn hàng:</span>
                </div>
                <div class="cover-row-left">
                    <span class="detail-row">Khối lượng: 500g</span>
                </div>
                <div class="cover-row-left">
                    <span class="detail-row">Tổng số lượng sản phẩm: {{ $order->total_item_count }}</span>
                </div>
            </div>

            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th class="row-header">STT</th>
                            <th class="row-header">Mã hàng</th>
                            <th class="row-header">SL</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="detail-row-center">{{ $loop->index + 1 }}</td>
                                <td class="detail-row-center">
                                    {{ $item->getTypeInstance()->getOrderedItem($item)->sku }}</td>
                                <td class="detail-row-center">{{ $item->qty_ordered }}</td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>

</html>
