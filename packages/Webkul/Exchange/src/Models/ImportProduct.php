<?php

namespace Webkul\Exchange\Models;

use Illuminate\Database\Eloquent\Model;

class ImportProduct extends Model// implements ImportProductContract
{
    // protected $guarded = ['_token'];
    public $timestamps = false;
    protected $fillable = [
        'type',
        'status',
        'created_user_id',
        'receipt_user_id',
        'supplier_id',
        'from_inventory_source_id',
        'to_inventory_source_id',
        'created_date',
        'updated_date',
        'transfer_date',
        'receipt_date',
        'note'
    ];
    protected $primaryKey = 'id';
    
    protected $table = 'exchange_notes';
}