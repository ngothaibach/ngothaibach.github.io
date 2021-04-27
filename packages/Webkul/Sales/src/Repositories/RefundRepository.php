<?php

namespace Webkul\Sales\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;
use Webkul\Sales\Contracts\Refund;
use Webkul\Core\Eloquent\Repository;
use Webkul\Checkout\Models\CartPayment;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Checkout\Repositories\CartItemRepository;
use Webkul\Checkout\Repositories\CartAddressRepository;
use Webkul\Sales\Repositories\OrderAddressRepository;
use Webkul\Sales\Models\OrderComment;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Repositories\InvoiceRepository;

class RefundRepository extends Repository
{
    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * OrderItemRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderItemRepository
     */
    protected $orderItemRepository;

    /**
     * RefundItemRepository object
     *
     * @var \Webkul\Sales\Repositories\RefundItemRepository
     */
    protected $refundItemRepository;

    /**
     * DownloadableLinkPurchasedRepository object
     *
     * @var \Webkul\Sales\Repositories\DownloadableLinkPurchasedRepository
     */
    protected $downloadableLinkPurchasedRepository;

    /**
     * CartRepository object
     *
     * @var \Webkul\Checkout\Repositories\CartRepository;
     */
    protected $cartRepository;

    /**
     * CartItemRepository object
     *
     * @var \Webkul\Checkout\Repositories\CartItemRepository;
     */
    protected $cartItemRepository;

    /**
     * CartAddressRepository object
     *
     * @var \Webkul\Checkout\Repositories\CartAddressRepository;
     */
    protected $cartAddressRepository;

    /**
     * OrderAddressRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderAddressRepository;
     */
    protected $orderAddressRepository;

    /**
     * InvoiceRepository object
     *
     * @var \Webkul\Sales\Repositories\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\OrderItemRepository  $orderItemRepository
     * @param  \Webkul\Sales\Repositories\RefundItemRepository   $refundItemRepository
     * @param  \Webkul\Sales\Repositories\DownloadableLinkPurchasedRepository  $downloadableLinkPurchasedRepository
     * @param  \Illuminate\Container\Container  $app
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        RefundItemRepository $refundItemRepository,
        DownloadableLinkPurchasedRepository $downloadableLinkPurchasedRepository,
        App $app,
        CartRepository $cartRepository,
        CartItemRepository $cartItemRepository,
        CartAddressRepository $cartAddressRepository,
        OrderAddressRepository $orderAddressRepository,
        InvoiceRepository $invoiceRepository
    )
    {
        $this->orderRepository = $orderRepository;

        $this->orderItemRepository = $orderItemRepository;

        $this->refundItemRepository = $refundItemRepository;

        $this->downloadableLinkPurchasedRepository = $downloadableLinkPurchasedRepository;

        $this->cartRepository = $cartRepository;

        $this->cartItemRepository = $cartItemRepository;

        $this->cartAddressRepository = $cartAddressRepository;

        $this->orderAddressRepository = $orderAddressRepository;

        $this->invoiceRepository = $invoiceRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Refund::class;
    }

    /**
     * @param  array  $data
     * @return \Webkul\Sales\Contracts\Refund
     */
    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            Event::dispatch('sales.refund.save.before', $data);

            $order = $this->orderRepository->find($data['order_id']);

            $totalQty = array_sum($data['refund']['items']);

            $refund = parent::create([
                'order_id'               => $order->id,
                'total_qty'              => $totalQty,
                'state'                  => 'refunded',
                'base_currency_code'     => $order->base_currency_code,
                'channel_currency_code'  => $order->channel_currency_code,
                'order_currency_code'    => $order->order_currency_code,
                // 'adjustment_refund'      => core()->convertPrice($data['refund']['adjustment_refund'], $order->order_currency_code),
                // 'base_adjustment_refund' => $data['refund']['adjustment_refund'],
                'adjustment_refund'      => core()->convertPrice(0, $order->order_currency_code),
                'base_adjustment_refund' => 0,
                // 'adjustment_fee'         => core()->convertPrice($data['refund']['adjustment_fee'], $order->order_currency_code),
                // 'base_adjustment_fee'    => $data['refund']['adjustment_fee'],
                'adjustment_fee'         => core()->convertPrice(0, $order->order_currency_code),
                'base_adjustment_fee'    => 0,
                // 'shipping_amount'        => core()->convertPrice($data['refund']['shipping'], $order->order_currency_code),
                // 'base_shipping_amount'   => $data['refund']['shipping'],
                'shipping_amount'        => core()->convertPrice(0, $order->order_currency_code),
                'base_shipping_amount'   => 0,
                'collection_diff'        => $order->collection_diff,
                'refund_fee'             => $data['refund_fee'],
                'discount_amount'        => $data['discount'],
                'base_discount_amount'   => $data['discount'],
            ]);

            foreach ($data['refund']['items'] as $itemId => $qty) {
                if (! $qty) {
                    continue;
                }

                $orderItem = $this->orderItemRepository->find($itemId);

                if ($qty > $orderItem->qty_to_refund) {
                    $qty = $orderItem->qty_to_refund;
                }

                $refundItem = $this->refundItemRepository->create([
                    'refund_id'            => $refund->id,
                    'order_item_id'        => $orderItem->id,
                    'name'                 => $orderItem->name,
                    'sku'                  => $orderItem->sku,
                    'qty'                  => $qty,
                    'price'                => $orderItem->price,
                    'base_price'           => $orderItem->base_price,
                    'total'                => $orderItem->price * $qty,
                    'base_total'           => $orderItem->base_price * $qty,
                    'tax_amount'           => ( ($orderItem->tax_amount / $orderItem->qty_ordered) * $qty ),
                    'base_tax_amount'      => ( ($orderItem->base_tax_amount / $orderItem->qty_ordered) * $qty ),
                    'discount_amount'      => ( ($orderItem->discount_amount / $orderItem->qty_ordered) * $qty ),
                    'base_discount_amount' => ( ($orderItem->base_discount_amount / $orderItem->qty_ordered) * $qty ),
                    'product_id'           => $orderItem->product_id,
                    'product_type'         => $orderItem->product_type,
                    'additional'           => $orderItem->additional,
                ]);

                if ($orderItem->getTypeInstance()->isComposite()) {
                    foreach ($orderItem->children as $childOrderItem) {
                        $finalQty = $childOrderItem->qty_ordered
                                    ? ($childOrderItem->qty_ordered / $orderItem->qty_ordered) * $qty
                                    : $orderItem->qty_ordered;

                        $refundItem->child = $this->refundItemRepository->create([
                            'refund_id'            => $refund->id,
                            'order_item_id'        => $childOrderItem->id,
                            'parent_id'            => $refundItem->id,
                            'name'                 => $childOrderItem->name,
                            'sku'                  => $childOrderItem->sku,
                            'qty'                  => $finalQty,
                            'price'                => $childOrderItem->price,
                            'base_price'           => $childOrderItem->base_price,
                            'total'                => $childOrderItem->price * $finalQty,
                            'base_total'           => $childOrderItem->base_price * $finalQty,
                            'tax_amount'           => 0,
                            'base_tax_amount'      => 0,
                            'discount_amount'      => 0,
                            'base_discount_amount' => 0,
                            'product_id'           => $childOrderItem->product_id,
                            'product_type'         => $childOrderItem->product_type,
                            'additional'           => $childOrderItem->additional,
                        ]);

                        if ($childOrderItem->getTypeInstance()->isStockable() || $childOrderItem->getTypeInstance()->showQuantityBox()) {
                            $this->refundItemRepository->returnQtyToProductInventory($childOrderItem, $finalQty);
                        }

                        $this->orderItemRepository->collectTotals($childOrderItem);
                    }

                } else {
                    if ($orderItem->getTypeInstance()->isStockable() || $orderItem->getTypeInstance()->showQuantityBox()) {
                        $this->refundItemRepository->returnQtyToProductInventory($orderItem, $qty);
                    }
                }

                $this->orderItemRepository->collectTotals($orderItem);

                if ($orderItem->qty_ordered == $orderItem->qty_refunded + $orderItem->qty_canceled) {
                    $this->downloadableLinkPurchasedRepository->updateStatus($orderItem, 'expired');
                }
            }

            $this->collectTotals($refund);

            $this->orderRepository->collectTotals($order);

            $this->orderRepository->updateOrderStatus($order);

            //minhpd thêm tạo đơn hàng mới khi đổi hàng
            $check = count($data['added_products']);
            if($check > 0) {
                $this->createOrderExchange($data, $refund);
            }

            Event::dispatch('sales.refund.save.after', $refund);
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        DB::commit();

        return $refund;
    }

    /**
     * @param  \Webkul\Sales\Contracts\Refund  $refund
     * @return \Webkul\Sales\Contracts\Refund
     */
    public function collectTotals($refund)
    {
        $refund->sub_total = $refund->base_sub_total = 0;
        $refund->tax_amount = $refund->base_tax_amount = 0;
        // $refund->discount_amount = $refund->base_discount_amount = 0;

        foreach ($refund->items as $refundItem) {
            $refund->sub_total += $refundItem->total;
            $refund->base_sub_total += $refundItem->base_total;

            $refund->tax_amount += $refundItem->tax_amount;
            $refund->base_tax_amount += $refundItem->base_tax_amount;

            // $refund->discount_amount += $refundItem->discount_amount;
            // $refund->base_discount_amount += $refundItem->base_discount_amount;
        }

        //minhpd sửa giảm giá 
        // if ($refund->order->discount_percent == 0) {
        //     $refund->discount_amount = $refund->base_discount_amount = $refund->order->discount_amount;
        // }

        $refund->grand_total = $refund->sub_total + $refund->tax_amount + $refund->shipping_amount + $refund->adjustment_refund - $refund->adjustment_fee - $refund->discount_amount + $refund->collection_diff - $refund->refund_fee;
        $refund->base_grand_total = $refund->base_sub_total + $refund->base_tax_amount + $refund->base_shipping_amount + $refund->base_adjustment_refund - $refund->base_adjustment_fee - $refund->base_discount_amount + $refund->collection_diff - $refund->refund_fee;

        $refund->save();

        return $refund;
    }

    /**
     * @param  array  $data
     * @param  integer  $orderId
     * @return array
     */
    public function getOrderItemsRefundSummary($data, $orderId)
    {
        $order = $this->orderRepository->find($orderId);

        $summary = [
            'subtotal'    => ['price' => 0],
            'discount'    => ['price' => 0],
            'tax'         => ['price' => 0],
            'shipping'    => ['price' => 0],
            'grand_total' => ['price' => 0],
            'collection_diff' => ['price' => 0],
        ];

        foreach ($data as $orderItemId => $qty) {
            if (! $qty) {
                continue;
            }

            $orderItem = $this->orderItemRepository->find($orderItemId);

            if ($qty > $orderItem->qty_to_refund) {
                return false;
            }

            $summary['subtotal']['price'] += $orderItem->base_price * $qty;

            $summary['discount']['price'] += ($orderItem->base_discount_amount / $orderItem->qty_ordered) * $qty;

            $summary['tax']['price'] += ($orderItem->tax_amount / $orderItem->qty_ordered) * $qty;
        }

        $summary['shipping']['price'] += $order->base_shipping_invoiced - $order->base_shipping_refunded - $order->base_shipping_discount_amount;

        $summary['collection_diff']['price'] = $order->collection_diff;
        $summary['discount']['price'] = $order->discount_amount;

        $summary['grand_total']['price'] += $summary['subtotal']['price'] + $summary['tax']['price'] + $summary['shipping']['price'] - $summary['discount']['price'] + $summary['collection_diff']['price'];

        $summary['subtotal']['formated_price'] = core()->formatBasePrice($summary['subtotal']['price']);

        $summary['discount']['formated_price'] = core()->formatBasePrice($summary['discount']['price']);

        $summary['collection_diff']['formated_price'] = core()->formatBasePrice($summary['collection_diff']['price']);

        $summary['tax']['formated_price'] = core()->formatBasePrice($summary['tax']['price']);

        $summary['shipping']['formated_price'] = core()->formatBasePrice($summary['shipping']['price']);

        $summary['grand_total']['formated_price'] = core()->formatBasePrice($summary['grand_total']['price']);

        return $summary;
    }

    //minhpd tạo đơn đổi hàng
    public function createOrderExchange($data, $refund){
        DB::beginTransaction();

        try {
            $order_old = $this->orderRepository->find($data['order_id']);
            $cart_old = DB::table('cart')->find($order_old->cart_id);
            $customer_id = $cart_old->customer_id;
            $customer = DB::table('customers')->find($customer_id);
            $customer_email= $customer->email;
            $customer_first_name= $customer->first_name;
            $customer_last_name= $customer->last_name;

            //lay du lieu address cua customer
            $addresses = DB::table('addresses')->where('customer_id',$customer_id)->where('address_type','=','customer')->first();
            $customer_company = $addresses->company_name;
            $customer_address1 = $addresses->address1;
            $customer_postcode = $addresses->postcode;
            $customer_city = $addresses->city;
            $customer_state = $addresses->state;
            $customer_country = $addresses->country;
            $customer_phone = $addresses->phone;

            $list_product = $data['added_products'];
            $total_item_count = count($list_product);
            $total_item_qty = 0;
            foreach ($list_product as $product )
            {
                $total_item_qty += $product['qty'];
            }

            $discount_amount = $data['discount_orders'];
            $discount_percent = 0;
            //lưu dữ liệu vào giỏ hàng
            $cartData = [
                'customer_email' => $customer_email,
                'customer_first_name' => $customer_first_name,
                'customer_last_name' => $customer_last_name,
                'shipping_method' => 'free_free',
                'is_gift' => 0,
                'items_count' => $total_item_count,
                'items_qty' => $total_item_qty,
                'global_currency_code' => 'VND',
                'base_currency_code' => 'VND',
                'channel_currency_code' => 'VND',
                'cart_currency_code' => 'VND',
                'grand_total' => $data['price_sum_total'],
                'base_grand_total' => $data['price_sum_total'],
                'sub_total' => $data['price_total'],
                'base_sub_total' => $data['price_total'],
                'tax_total' => 0,
                'base_tax_total' => 0,
                'discount_amount' => $discount_amount,
                'base_discount_amount' => $discount_amount,
                'is_guest' => 1,
                'is_active' => 0,
                'customer_id' => $customer_id,
                'channel_id' => 1,
                // 'created_at' => new DateTime(),
                // 'updated_at' => new DateTime(),
            ];

            $cart = $this->cartRepository->create($cartData);

            //luu du lieu item trong gio hang
            foreach ($list_product as $product ) {
                $productsObject = DB::table('products')->find($product['id']);   
                    $cartItemData = [
                        'quantity' => $product['qty'],
                        'sku' => $productsObject->sku,
                        'type' => 'simple',
                        'name' => $product['name'],
                        'weight' => $product['price'],
                        'total_weight' => $product['price'],
                        'base_total_weight' => $product['price'],
                        'price' => $product['price'],
                        'base_price' => $product['price'],
                        'total' => $product['price'] * $product['qty'],
                        'base_total' => $product['price'] * $product['qty'],
                        'base_tax_amount' => 0,
                        'discount_percent' => $discount_percent,
                        'discount_amount' => ($discount_percent * $product['price'] * $product['qty'])/100,
                        'base_discount_amount' => ($discount_percent * $product['price'] * $product['qty'])/100,
                        'product_id' => $product['id'],
                        'cart_id' => $cart->id,
                    ];
                    $cartItem = $this->cartItemRepository->create($cartItemData);
            }
            //luu du lieu cart payment
            $cartPayment = new CartPayment();
            $cartPayment->method = "cashondelivery";
            $cartPayment->cart_id = $cart->id;
            $cartPayment->save();

            //Lưu dữ liệu cart vào address cart_billing
            $arr_address_cart = array('cart_billing', 'cart_shipping');
            foreach($arr_address_cart as $value) {
                $cartAddressData = [
                    'address_type' => $value,
                    'cart_id' => $cart->id,
                    'first_name' => $customer_first_name,
                    'last_name' => $customer_last_name,
                    'company_name' => $customer_company,
                    'address1' => $customer_address1,
                    'postcode' => $customer_postcode,
                    'city' => $customer_city,
                    'state' => $customer_state,
                    'country' => $customer_country,
                    'email' => $customer_email,
                    'phone' => $customer_phone,
                    'default_address' => 0,
                ];
                $cartAddress = $this->cartAddressRepository->create($cartAddressData);
                
            }

            //lưu dữ liệu vào orders
            $order = new Order();
            $order->increment_id = $this->orderRepository->generateIncrementId();
            $order->status = 'pending';
            $order->channel_name = 'Offline';
            $order->is_guest = 1;
            $order->customer_email = $customer_email;
            $order->customer_first_name = $customer_first_name;
            $order->customer_last_name = $customer_last_name;
            $order->shipping_method = 'free_free';
            $order->shipping_title = 'Free Shipping - Free Shipping';
            $order->shipping_description = 'Free Shipping';
            $order->is_gift = 0;
            $order->total_item_count = $total_item_count;
            $order->total_qty_ordered = $total_item_qty;
            $order->base_currency_code = 'VND';
            $order->channel_currency_code = 'VND';
            $order->order_currency_code = 'VND';
            $order->grand_total = $data['price_sum_total'];
            $order->base_grand_total = $data['price_sum_total'];
            $order->sub_total = $data['price_total'];
            $order->base_sub_total = $data['price_total'];
            $order->channel_id = 1;
            $order->channel_type = 'Webkul\Core\Models\Channel';
            $order->discount_amount = $discount_amount;
            $order->discount_percent = $discount_percent;
            $order->cart_id = $cart->id;
            $order->collection_diff = $data['collection_diff_orders'];
            $order->sales_id = $order_old->sales_id;
            $order->inventory_id = $order_old->inventory_id;

            $money_must_back = str_replace(",","",$data['money_must_back']);
            if ($data['price_sum_total'] > (int)($money_must_back))
            {
                $order->customer_paid = $data['price_must_paid'];
                $order->customer_remain = 0;
            } elseif($data['price_sum_total'] < (int)($money_must_back))
            {
                $order->customer_paid = 0;
                $order->customer_remain = $data['price_must_paid'];
            } else {
                $order->customer_paid = 0;
                $order->customer_remain = 0;
            }

            $order->refund_exchange_id = $refund->id;
            $order->save();

            //luư dữ liệu vào order items

            foreach (request()->added_products as $product ) {
                $productsObject = DB::table('products')->find($product['id']);       
                $orderItemItemData = [
                    'sku' => $productsObject->sku,
                    'name' => $product['name'],
                    'type' => 'simple',
                    'weight' => $product['price'],
                    'total_weight' => $product['price'],
                    'qty_ordered' => $product['qty'],
                    'price' => $product['price'],
                    'base_price' => $product['price'],
                    'total' => $product['price'] * $product['qty'],
                    'base_total' => $product['price'] * $product['qty'],
                    'discount_percent' => $discount_percent,
                    'discount_amount' => ($discount_percent * $product['price'] * $product['qty'])/100,
                    'base_discount_amount' => ($discount_percent * $product['price'] * $product['qty'])/100,
                    'product_id' => $product['id'],
                    'product_type' => 'Webkul\Product\Models\Product',
                    'order_id' => $order->id,
                ];
                $orderItem = $this->orderItemRepository->create($orderItemItemData);
            }

            //lưu dữ liệu vào order payment
            $orderPayment = new OrderPayment();
            $orderPayment->method = "cashondelivery";
            $orderPayment->order_id = $order->id;
            $orderPayment->save();

            //Lưu dữ liệu vào address order_billing, order_shipping
            $arr_address_order = array('order_billing', 'order_shipping');
            foreach($arr_address_order as $value) {
                $orderAddressData = [
                    'address_type' => $value,
                    'order_id' => $order->id,
                    'first_name' => $customer_first_name,
                    'last_name' => $customer_last_name,
                    'company_name' => $customer_company,
                    'address1' => $customer_address1,
                    'postcode' => $customer_postcode,
                    'city' => $customer_city,
                    'state' => $customer_state,
                    'country' => $customer_country,
                    'email' => $customer_email,
                    'phone' => $customer_phone,
                    'default_address' => 0,
                ];
                $orderAddress = $this->orderAddressRepository->create($orderAddressData);
            }

            //lưu dữ liệu vào bảng ordercomment
            if($data['notes'] != "")
            {
                $orderComent = new OrderComment();
                $orderComent->comment = $data['notes'];
                $orderComent->order_id = $order->id;
                $orderComent->customer_notified = 0;
                $orderComent->save();
            }

            //tạo hóa đơn
            $data_invoice = array();
            foreach ($order->items as $item ) {
                array_push($data_invoice,$data_invoice["invoice"]["items"][$item->id] = $item->qty_ordered);
            }
            $this->invoiceRepository->create(array_merge($data_invoice, ['order_id' => $order->id]));


        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }

        DB::commit();
    }
}