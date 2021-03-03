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
    public function update(array $data, $id, $attribute = "id")
    {
        $product_exchange_note = $this->find($id);
        $product_exchange_note->update($data);
        return $product_exchange_note;
    }
}