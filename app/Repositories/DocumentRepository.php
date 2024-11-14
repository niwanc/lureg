<?php

namespace App\Repositories;

use App\Helpers\PDFSanitizer;
use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentRepository implements DocumentRepositoryInterface
{
    /**
     * Retrieve all documents for the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Document::where('user_id', auth()->id())->get();
    }

    /**
     * Create a new document for the authenticated user.
     *
     * @param \Illuminate\Http\UploadedFile $document
     * @return \Illuminate\Http\JsonResponse|null
     */
    public function create($document): ?Document
    {

        // Sanitize the PDF file
        $sanitizer = new PDFSanitizer();
        try {
            $response = $sanitizer->sanitize($document);
            if($response->getStatusCode() === 400) {
                return null;
            }
        } catch (\Exception $e) {
            //todo validate empty and other formatting errors
            if(config('app.env') === 'production') {
                return null;
            }
            Log::info('PDFSanitizer failed: ' . $e->getMessage());
        }

        $fileName = time() . '_' . Str::uuid() . '.' . $document->extension();
        $filePath = $document->storeAs('documents', $fileName);

        // Create a new document
        return Document::create([
            'user_id' => auth()->id(),
            'file_path' => $filePath,
            'status' => 'pending',
            'uuid' => Str::uuid(),
        ]);
    }

    /**
     * Update an existing document by its ID.
     *
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($uuid, $data): int
    {
        return Document::where('uuid', $uuid)->update($data);
    }

    /**
     * Find a document by its ID.
     *
     * @param int $id
     * @return \App\Models\Document|null
     */
    public function find($uuid): ?Document
    {
        return Document::where('uuid', $uuid)
            ->first();
    }

    /**
     * Delete a document by its ID.
     *
     * @param int $id
     * @return int
     */
    public function delete($uuid): int
    {
        return Document::where('uuid', $uuid)
            ->where('user_id', auth()->id())
            ->delete();
    }

}
