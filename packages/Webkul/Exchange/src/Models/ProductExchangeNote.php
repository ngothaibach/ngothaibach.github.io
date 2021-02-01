<?php

namespace Webkul\Exchange\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Exchange\Contracts\ProductExchangeNote as ProductExchangeNoteContract;

class ProductExchangeNote extends Model// implements ProductExchangeNoteContract
{
    protected $guarded = ['_token'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_exchange_notes';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}