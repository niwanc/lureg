<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {
    }

    public function create(array $data)
    {
        return $this->userRepository->create($data);
    }
}
