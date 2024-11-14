<?php

namespace App\Services;

use App\Repositories\DocumentRepositoryInterface;

class DocumentService
{
    public function __construct(
        protected DocumentRepositoryInterface $documentRepository
    ) {
    }

    public function all()
    {
        return $this->documentRepository->all();
    }

    public function create($document)
    {
        return $this->documentRepository->create($document);
    }

    public function find($uuid)
    {
        return $this->documentRepository->find($uuid);
    }

    public function delete($uuid)
    {
        return $this->documentRepository->delete($uuid);
    }

    public function update($uuid, $data)
    {
        return $this->documentRepository->update($uuid, $data);
    }
}
