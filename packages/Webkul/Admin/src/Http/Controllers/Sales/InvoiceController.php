<?php

namespace Webkul\Admin\Http\Controllers\Sales;

use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use PDF;
use Illuminate\Support\Facades\DB;
use Session;


class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * InvoiceRepository object
     *
     * @var \Webkul\Sales\Repositories\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\InvoiceRepository  $invoiceRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository
    )
    {
        $this->middleware('admin');

        $this->_config = request('_config');

        $this->orderRepository = $orderRepository;

        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $query = DB::table('invoices')
            ->leftJoin('orders as ors', 'invoices.order_id', '=', 'ors.id')
            ->leftJoin('order_comments as comments','invoices.order_id','=','comments.order_id')
            ->leftJoin('order_payment as payments','invoices.order_id','=','payments.order_id')
            ->select('invoices.id as id','ors.customer_first_name as first_name','ors.customer_last_name as last_name',
            'ors.status as status','ors.channel_name as channel_name','ors.collection_diff as collection_diff',
            'payments.method as method_payment','comments.comment as comment','ors.base_tax_amount as base_tax_amount',
            'invoices.base_grand_total as base_grand_total','ors.base_sub_total as base_sub_total', 'ors.increment_id as order_id', 
            'invoices.state as state', 'invoices.base_discount_amount as base_discount_amount', 'invoices.created_at as created_at');
        if( Session::get('inventory') != 0){
            $query = $query->where('ors.inventory_id','=',Session::get('inventory'));
        }
        $invoice_note = $query->get()-> toArray();
        $role_id = auth()->guard('admin')->user()->role['id'];      
        return view($this->_config['view'], compact('role_id','invoice_note'));

        // return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $orderId
     * @return \Illuminate\View\View
     */
    public function create($orderId)
    {
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

        if (! $order->canInvoice()) {
            session()->flash('error', trans('admin::app.sales.invoices.creation-error'));

            return redirect()->back();
        }

        $this->validate(request(), [
            'invoice.items.*' => 'required|numeric|min:0',
        ]);

        $data = request()->all();

        $haveProductToInvoice = false;

        foreach ($data['invoice']['items'] as $itemId => $qty) {
            if ($qty) {
                $haveProductToInvoice = true;
                break;
            }
        }

        if (! $haveProductToInvoice) {
            session()->flash('error', trans('admin::app.sales.invoices.product-error'));

            return redirect()->back();
        }

        $this->invoiceRepository->create(array_merge($data, ['order_id' => $orderId]));

        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Invoice']));

        return redirect()->route($this->_config['redirect'], $orderId);
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */

    public function show_detail_invoice()
    {
        $invoice_id = request()->input('invoice_id');

        $invoice = DB::table('invoice_items')
        ->where('invoice_id', '=', $invoice_id)
        ->get()-> toArray();

        // $invoice = $this->invoiceRepository->findOrFail($invoice_id);
        return response()->json(
            [
                'success' => True,
                'invoice_product' => $invoice
            ]
        );
    }
    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */

    public function update_notes()
    {
        $order_id = request()->input('order_id');
        $comment_content = request()->input('comment_content');
        $invoice = DB::table('order_comments')
        ->where('order_id', '=', $order_id)->first();
        if(!$invoice){
            DB::table('order_comments')->insert([
                'comment' => $comment_content,
                'customer_notified' => 0,
                'order_id' => $order_id
            ]);
        }else{
            DB::table('order_comments')
              ->where('order_id', '=', $order_id)
              ->update(['comment' => $comment_content]);
        }
        // ->get()-> toArray();
        // $invoice = $this->invoiceRepository->findOrFail($invoice_id);
        session()->flash('success','Cập nhật thành công');

        return response()->json(
            [
                'success' => True,
                'invoice_product' => '12345'
            ]
        );
    }

    public function view($id)
    {
        
        $invoice = $this->invoiceRepository->findOrFail($id);

        return view($this->_config['view'], compact('invoice'));
    }

    /**
     * Print and download the for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print($id)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        $pdf = PDF::loadView('admin::sales.invoices.pdf', compact('invoice'))->setPaper('a4');

        return $pdf->download('invoice-' . $invoice->created_at->format('d-m-Y') . '.pdf');
    }

    public function print_invoices()
    {
        $id = request()->input('id');
        
        $invoice = $this->invoiceRepository->findOrFail($id);

        $pdf = PDF::loadView('admin::sales.invoices.pdf', compact('invoice'))->setPaper('a4');

        return $pdf->download('invoice-' . $invoice->created_at->format('d-m-Y') . '.pdf');
    }
}
