<?php

namespace Webkul\Admin\DataGrids;
use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Session;
class ReportExchangeDataGrid extends DataGrid {
    protected $index = 'ma_chuyen_hang';

    // protected $sortOrder = 'desc';

    // protected $itemsPerPage = 10;

    public function prepareQueryBuilder()
    {
        $data = request()->all();
        $from_date = $data['from_date']." 00:00:00";
        $to_date = $data['to_date']." 23:59:59";
        $exchange_id = $data['exchange_id'];
        $from_inventory_id = $data['from_inventory_id'];
        $to_inventory_id = $data['to_inventory_id'];
       $queryBuilder = DB::table('exchange_notes as exn')
        ->join('suppliers as spl', 'spl.id', '=', 'exn.supplier_id')
        ->join('inventory_sources as ins', 'ins.id', '=', 'exn.to_inventory_source_id')
        ->join('inventory_sources as ins1', 'ins1.id', '=', 'exn.from_inventory_source_id')
        ->join('admins as ad', 'ad.id', '=', 'exn.created_user_id')
        ->join('product_exchange_notes as pexc', 'pexc.exchange_note_id', '=', 'exn.id')
        ->leftJoin('product_flat as pf', 'pf.product_id', '=', 'pexc.product_id')
        
        // ->select('exchange_notes.id', 'exchange_notes.created_date', 'exchange_notes.note', 'exchange_notes.status','exchange_notes.type','exchange_notes.importer', 'exchange_notes.receipt_date', 'suppliers.name as supplier', 'inventory_sources.name as inventory','inventory_sources.id as inventoryID', 'admins.name as created_user','exchange_notes.total')
        ->addSelect('exn.id as ma_chuyen_hang','exn.type as loai_phieu','ins1.name as tu_chi_nhanh')
        ->addSelect('ins.name as toi_chi_nhanh','exn.transfer_date as ngay_chuyen','exn.created_date as thoi_gian_tao')
        ->addSelect('exn.receipt_date as ngay_nhan','ad.name as nguoi_tao','exn.note as ghi_chu')
        ->addSelect('pexc.transfer_qty as tong_sl_chuyen',DB::raw('(pexc.transfer_qty * pexc.price) as tong_tien_chuyen'))
        ->addSelect('pexc.receipt_qty as tong_sl_nhan',DB::raw('(pexc.receipt_qty * pexc.price) as tong_tien_nhan'))
        ->addSelect('exn.status as trang_thai','pf.sku as ma_hh','pf.name as ten_hh')
        ->where('exn.created_date','>=', $from_date)
        ->where('exn.created_date','<=', $to_date);
        // dd($exchange_id);
        if($exchange_id != "0"){
            $queryBuilder->where('exn.type', $exchange_id);
        }
        if ($from_inventory_id != 0) {
            $queryBuilder->where('exn.from_inventory_source_id',$from_inventory_id);
        }
        if ($to_inventory_id != 0) {
            $queryBuilder->where('exn.to_inventory_source_id',$to_inventory_id);
        }
        
        $this->setQueryBuilder($queryBuilder);
    }

    // public function headings()
    // {
    //     return ["Mã đơn hàng", "Ngày tạo", "Tên khách hàng", "Giá trị đơn hàng", "Kho", "Người bán", "Trạng thái đơn hàng"];
    // }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'ma_chuyen_hang',
            'label'      => 'ma_chuyen_hang',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        // $this->addColumn([
        //     'index'      => 'ngay_tao',
        //     'label'      => 'Ngày mua',
        //     'type'       => 'string',
        //     'searchable' => true,
        //     'sortable'   => true,
        //     'filterable' => true,
        // ]);

        // $this->addColumn([
        //     'index'      => 'ten_khach_hang',
        //     'label'      => 'Tên khách hàng',
        //     'type'       => 'string',
        //     'searchable' => true,
        //     'sortable'   => true,
        //     'filterable' => true,
        // ]);

        // $this->addColumn([
        //     'index'      => 'gia_tien',
        //     'label'      => 'Giá tiền',
        //     'type'       => 'number',
        //     'searchable' => false,
        //     'sortable'   => true,
        //     'filterable' => true,
        // ]);

        // $this->addColumn([
        //     'index'      => 'kho',
        //     'label'      => 'Kho',
        //     'type'       => 'string',
        //     'searchable' => true,
        //     'sortable'   => true,
        //     'filterable' => false,
        // ]);

        // $this->addColumn([
        //     'index'      => 'nguoi_ban',
        //     'label'      => 'Người bán',
        //     'type'       => 'string',
        //     'searchable' => false,
        //     'sortable'   => true,
        //     'filterable' => false,
        // ]);

        // $this->addColumn([
        //     'index'      => 'trang_thai_don_hang',
        //     'label'      => 'Trạng thái đơn hàng',
        //     'type'       => 'boolean',
        //     'searchable' => false,
        //     'sortable'   => true,
        //     'filterable' => true,
        // ]);
    }
}