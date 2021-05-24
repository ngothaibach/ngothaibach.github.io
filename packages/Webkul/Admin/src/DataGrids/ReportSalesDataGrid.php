<?php

namespace Webkul\Admin\DataGrids;
use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Session;
class ReportSalesDataGrid extends DataGrid {
    protected $index = 'id';

    protected $sortOrder = 'desc';

    // protected $itemsPerPage = 10;

    public function prepareQueryBuilder()
    {
        $data = request()->all();
        $from_date = $data['from_date']." 00:00:00";
        $to_date = $data['to_date']." 23:59:59";
        // $to_date = $to_date;
        $inventory_id = $data['inventory_id'];
        $sale_id = $data['sale_id'];

        $queryBuilder = DB::table('orders as ord')
            ->leftJoin('inventory_sources as ins', 'ord.inventory_id', '=', 'ins.id')
            ->leftJoin('admins as ad', 'ad.id', '=', 'ord.sales_id')
            ->leftJoin('cat_status as cs', 'cs.id', '=', 'ord.status_id')
            ->Join('order_items as oi', 'oi.order_id', '=', 'ord.id')
            ->addSelect('ord.id', 'ord.created_at as ngay_tao', 'ord.grand_total as gia_tien', 'ins.name as kho', 'ad.name as nguoi_ban', 'cs.name as trang_thai_don_hang')
            ->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'ord.customer_first_name, " ", ' . DB::getTablePrefix() . 'ord.customer_last_name) as ten_khach_hang'))
            ->addSelect('oi.sku as ma_hh', 'oi.name as ten_hh', 'oi.qty_ordered as so_luong', 'oi.price as don_gia', 'oi.total as thanh_tien', 'oi.qty_refunded as so_luong_tra')
            ->where('ord.created_at','>=', $from_date)
            ->where('ord.created_at','<=', $to_date);
        if ($inventory_id != 0) {
            $queryBuilder->where('ord.inventory_id',$inventory_id);
        }
        if ($sale_id != 0) {
            $queryBuilder->where('ord.sales_id',$sale_id);
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
            'index'      => 'id',
            'label'      => 'id',
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