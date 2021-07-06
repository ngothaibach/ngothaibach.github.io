<?php

namespace Webkul\Admin\Http\Controllers\Sales;

use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\OrderItemRepository;
use Webkul\Sales\Repositories\RefundRepository;
use Webkul\Sales\Models\OrderAddress;
use Illuminate\Support\Facades\DB;
use Session;

class RefundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @var array
     */
    protected $_config;

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
     * RefundRepository object
     *
     * @var \Webkul\Sales\Repositories\RefundRepository
     */
    protected $refundRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\OrderItemRepository  $orderItemRepository
     * @param  \Webkul\Sales\Repositories\RefundRepository  $refundRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        RefundRepository $refundRepository
    )
    {
        $this->middleware('admin');

        $this->_config = request('_config');

        $this->orderRepository = $orderRepository;

        $this->orderItemRepository = $orderItemRepository;

        $this->refundRepository = $refundRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\View
     */
    public function index()
    {
        $query = DB::table('refunds')
            ->select('refunds.id', 'orders.increment_id', 'refunds.state', 'refunds.base_grand_total', 'refunds.created_at')
            ->leftJoin('orders', 'refunds.order_id', '=', 'orders.id')
            ->leftJoin('addresses as order_address_billing', function($leftJoin) {
                $leftJoin->on('order_address_billing.order_id', '=', 'orders.id')
                         ->where('order_address_billing.address_type', OrderAddress::ADDRESS_TYPE_BILLING);
            })
            ->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_billing.first_name, " ", ' . DB::getTablePrefix() . 'order_address_billing.last_name) as billed_to'));
        if( Session::get('inventory') != 0){
            $query = $query->where('orders.inventory_id','=',Session::get('inventory'));
        };
        $query = $query->orderBy('increment_id', 'desc');
        $refunds_list=$query->get()->toArray();
        return view($this->_config['view'], compact('refunds_list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\View
     */
    public function create($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        return view($this->_config['view'], compact('order'));
    }
    public function create_refunds()
    {
        $orderId = request()->input('id');
        $order = $this->orderRepository->findOrFail($orderId);
        return view($this->_config['view'], compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function store($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if (! $order->canRefund()) {
            session()->flash('error', trans('admin::app.sales.refunds.creation-error'));

            return redirect()->back();
        }

        $this->validate(request(), [
            'refund.items.*' => 'required|numeric|min:0',
        ]);

        $data = request()->all();

        // if (! $data['refund']['shipping']) {
        //     $data['refund']['shipping'] = 0;
        // }

        $totals = $this->refundRepository->getOrderItemsRefundSummary($data['refund']['items'], $orderId);

        // $maxRefundAmount = $totals['grand_total']['price'] - $order->refunds()->sum('base_adjustment_refund') ;
        $maxRefundAmount = $totals['grand_total']['price']  ;

        // $refundAmount = $totals['grand_total']['price'] - $totals['shipping']['price'] + $data['refund']['shipping'] + $data['refund']['adjustment_refund'] - $data['refund']['adjustment_fee'] ;
        $refundAmount = $totals['grand_total']['price'] ;

        if (! $refundAmount) {
            session()->flash('error', trans('admin::app.sales.refunds.invalid-refund-amount-error'));

            return redirect()->back();
        }

        if ($refundAmount > $maxRefundAmount) {
            session()->flash('error', trans('admin::app.sales.refunds.refund-limit-error', ['amount' => core()->formatBasePrice($maxRefundAmount)]));

            return redirect()->back();
        }

        $this->refundRepository->create(array_merge($data, ['order_id' => $orderId]));

        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Refund']));

        // return redirect()->route($this->_config['redirect'], $orderId);
        return response()->json(
            [
                'success' => true,
                'message' => 'Save susscessfully',
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQty($orderId)
    {
        $data = $this->refundRepository->getOrderItemsRefundSummary(request()->all(), $orderId);

        if (! $data) {
            return response('');
        }

        return response()->json($data);
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\View
     */
    public function view($id)
    {
        $refund = $this->refundRepository->findOrFail($id);

        return view($this->_config['view'], compact('refund'));
    }
    
    public function view_refund()
    {
        $id = request()->input('refund_id');
        $refund = $this->refundRepository->findOrFail($id);
        return view($this->_config['view'], compact('refund'));
    }
}
