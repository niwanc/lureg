<?php

namespace App\Repositories;

interface DocumentRepositoryInterface
{
    public function all();

    public function create(array $document);

    public function find($id);

    public function delete($id);

    public function update($id, $data);

}
