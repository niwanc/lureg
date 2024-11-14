<?php

namespace App\Services;

use App\Repositories\SignatureRepositoryInterface;

class SignatureService
{
    public function __construct(
        protected SignatureRepositoryInterface $signatureRepository
    ) {
    }

    public function create(array $data)
    {
        return $this->signatureRepository->create($data);
    }
}
