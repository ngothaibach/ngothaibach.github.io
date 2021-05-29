<?php

namespace Webkul\Admin\DataGrids;
use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Session;
class ReportStaffDataGrid extends DataGrid {
    protected $index = 'nguoi_ban';

    // protected $sortOrder = 'desc';

    // protected $itemsPerPage = 10;

    public function prepareQueryBuilder()
    {
        $data = request()->all();
        $from_date = $data['from_date']." 00:00:00";
        $to_date = $data['to_date']." 23:59:59";
        $sale_id = $data['sale_id'];

        $queryBuilder = DB::table('orders as ord')
            ->join('admins as ad', 'ad.id', '=', 'ord.sales_id')
            ->leftJoin('refunds as rf', 'ord.id', '=', 'rf.order_id')
            ->addSelect('ad.name as nguoi_ban')
            ->addSelect(DB::raw('COUNT('.DB::getTablePrefix().'ord.id) as sl_don'))
            ->addSelect(DB::raw('sum('.DB::getTablePrefix().'ord.grand_total) as tong_tien'))
            ->addSelect(DB::raw('DATE_FORMAT('.DB::getTablePrefix().'ord.created_at,  "%d/%m/%Y") as ngay_ban'))
            ->addSelect(DB::raw('sum('.DB::getTablePrefix().'ord.discount_amount) as tong_tien_giam'))
            ->addSelect(DB::raw('sum('.DB::getTablePrefix().'ord.collection_diff) as tong_tien_thu_khac'))
            ->addSelect(DB::raw('COUNT('.DB::getTablePrefix().'rf.id) as tong_don_hoan_lai'))
            ->addSelect(DB::raw('sum('.DB::getTablePrefix().'ord.grand_total_refunded) as tong_tien_hoan_lai'))
            ->where('ord.created_at','>=', $from_date)
            ->where('ord.created_at','<=', $to_date);
        if ($sale_id != 0) {
            $queryBuilder->where('ord.sales_id',$sale_id);
        }
        $queryBuilder->groupBy('ad.name');
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
            'index'      => 'nguoi_ban',
            'label'      => 'nguoi_ban',
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