<?php

namespace App\Repositories;

use App\Models\Signature;

class SignatureRepository implements SignatureRepositoryInterface
{
    public function create(array $data)
    {
        return Signature::create($data);
    }
}
