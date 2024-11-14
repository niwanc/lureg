<?php

namespace App\Repositories;

interface SignatureRequestRepositoryInterface
{
    public function find($id);
    public function create(array $data);

    public function update($id, $data);

}
