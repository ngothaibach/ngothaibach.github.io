<?php

namespace Webkul\Exchange\Imports;

use Webkul\Exchange\Models\ImportProduct;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportExcel implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ImportProduct([
            'type'=> $row[0],
            'status'=> $row[1],
            'created_user_id'=> $row[2],
            'receipt_user_id'=> $row[3],
            'supplier_id'=> $row[4],
            'from_inventory_source_id'=> $row[5],
            'to_inventory_source_id'=> $row[6],
            'created_date'=> $row[7],
            'updated_date'=> $row[8],
            'transfer_date'=> $row[9],
            'receipt_date'=> $row[10],
            'note'=> $row[11],
        ]);
    }
}
