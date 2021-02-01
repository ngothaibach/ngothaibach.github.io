<?php

namespace Webkul\Exchange\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Exchange\Contracts\ExchangeNote as ExchangeNoteContract;

class ExchangeNote extends Model// implements ExchangeNoteContract
{
    protected $guarded = ['_token'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'exchange_notes';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}