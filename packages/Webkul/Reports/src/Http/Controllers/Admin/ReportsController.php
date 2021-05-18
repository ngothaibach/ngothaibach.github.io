<?php

namespace Webkul\Reports\Http\Controllers\Admin;
use Session;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Webkul\Sales\Repositories\OrderRepository;
use PDF;
use Excel;
use Webkul\Admin\Exports\DataGridExport;

class ReportsController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->middleware('admin');

        $this->_config = request('_config');

        $this->orderRepository = $orderRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user_sale = DB::table('admins')
        ->select('id','name')
        ->get()->toArray();

        $inventory_sources = DB::table('inventory_sources')->select('id', 'name')->get();

        return view($this->_config['view'], compact('user_sale', 'inventory_sources'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view($this->_config['view']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return view($this->_config['view']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }

    public function export_sales_report()
    {
        $gridName = 'Báo cáo bán hàng';

        $path = '\Webkul\Admin\DataGrids\\ReportSalesDataGrid';

        $gridInstance = new $path;
        
        $records = $gridInstance->export();
        if (! count($records)) {
            session()->flash('warning', trans('admin::app.export.no-records'));

            return redirect()->back();
        }
        return Excel::download(new DataGridExport($records), $gridName.'.xlsx');

        // session()->flash('warning', trans('admin::app.export.illegal-format'));

        // return redirect()->back();
    }
}
