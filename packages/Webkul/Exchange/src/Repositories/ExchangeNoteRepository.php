<?php

namespace Webkul\Exchange\Repositories;
use Webkul\Core\Eloquent\Repository;
class ExchangeNoteRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\Exchange\Models\ExchangeNote';
    }
    public function update(array $data, $id, $attribute = "id")
    {
        $exchange_note = $this->find($id);
        $exchange_note->update($data);
        return $exchange_note;
    }
}