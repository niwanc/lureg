<?php

namespace App\Services;

use App\Repositories\SignatureRequestRepository;
use App\Repositories\UserRepositoryInterface;

class SignatureRequestService
{
    public function __construct(
        protected SignatureRequestRepository $signatureRequestRepository
    ) {
    }

    public function find($id)
    {
        return $this->signatureRequestRepository->find($id);
    }
    public function create(array $data)
    {
        return $this->signatureRequestRepository->create($data);
    }

    public function update($id, $data)
    {
        return $this->signatureRequestRepository->update($id, $data);
    }
}
