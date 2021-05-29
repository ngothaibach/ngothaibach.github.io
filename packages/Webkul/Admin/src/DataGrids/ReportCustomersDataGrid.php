<?php

namespace Webkul\Admin\DataGrids;
use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Session;
class ReportCustomersDataGrid extends DataGrid {
    protected $index = 'ma_kh';

    // protected $sortOrder = 'desc';

    // protected $itemsPerPage = 10;

    public function prepareQueryBuilder()
    {
        $data = request()->all();
        $from_date = $data['from_date']." 00:00:00";
        $to_date = $data['to_date']." 23:59:59";
        $inventory_id = $data['inventory_id'];

        $queryBuilder = DB::table('orders as ord')
            ->join('cart as car', 'car.id', '=', 'ord.cart_id')
            ->leftJoin('refunds as rf', 'ord.id', '=', 'rf.order_id')
            ->leftJoin('inventory_sources as ins', 'ord.inventory_id', '=', 'ins.id')
            ->addSelect('car.customer_id as ma_kh')
            ->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'ord.customer_first_name, " ", ' . DB::getTablePrefix() . 'ord.customer_last_name) as ten_khach_hang'))
            ->addSelect(DB::raw('COUNT('.DB::getTablePrefix().'ord.id) as sl_don'))
            ->addSelect(DB::raw('sum('.DB::getTablePrefix().'ord.grand_total) as tong_tien'))
            ->addSelect(DB::raw('DATE_FORMAT('.DB::getTablePrefix().'ord.created_at,  "%d/%m/%Y") as ngay_ban'))
            ->addSelect(DB::raw('sum('.DB::getTablePrefix().'ord.discount_amount) as tong_tien_giam'))
            ->addSelect(DB::raw('sum('.DB::getTablePrefix().'ord.collection_diff) as tong_tien_thu_khac'))
            ->addSelect(DB::raw('COUNT('.DB::getTablePrefix().'rf.id) as tong_don_hoan_lai'))
            ->addSelect(DB::raw('sum('.DB::getTablePrefix().'ord.grand_total_refunded) as tong_tien_hoan_lai'))
            ->addSelect('ins.name as kho')
            ->where('car.customer_id', '<>', 'NULL')
            ->where('ord.created_at','>=', $from_date)
            ->where('ord.created_at','<=', $to_date);
        if ($inventory_id != 0) {
            $queryBuilder->where('ord.inventory_id',$inventory_id);
        }
        $queryBuilder->groupBy('car.customer_id');
        $queryBuilder->groupBy(DB::raw('CONCAT(' . DB::getTablePrefix() . 'ord.customer_first_name, " ", ' . DB::getTablePrefix() . 'ord.customer_last_name)'));
        $queryBuilder->groupBy(DB::raw('DATE_FORMAT('.DB::getTablePrefix().'ord.created_at,  "%d/%m/%Y")'));
        $queryBuilder->orderBy(DB::raw('DATE_FORMAT('.DB::getTablePrefix().'ord.created_at,  "%d/%m/%Y")', 'DESC'));
        $this->setQueryBuilder($queryBuilder);
    }

    // public function headings()
    // {
    //     return ["Mã đơn hàng", "Ngày tạo", "Tên khách hàng", "Giá trị đơn hàng", "Kho", "Người bán", "Trạng thái đơn hàng"];
    // }

    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'ma_kh',
            'label'      => 'ma_kh',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }
}