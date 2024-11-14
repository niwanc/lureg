<?php

namespace App\Repositories;

use App\Models\SignatureRequest;

class SignatureRequestRepository implements SignatureRequestRepositoryInterface
{
    public function find($id)
    {
        return SignatureRequest::where('id', $id)->first();
    }
    public function create(array $data)
    {
        return SignatureRequest::create($data);
    }

    public function update($id, $data)
    {
        return SignatureRequest::where('id', $id)->update($data);
    }
}
