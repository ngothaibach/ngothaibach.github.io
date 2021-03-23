<?php

namespace Webkul\Exchange\Repositories;

use Webkul\Core\Eloquent\Repository;

class ProductExchangeNoteRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Exchange\Models\ProductExchangeNote';
    }
}