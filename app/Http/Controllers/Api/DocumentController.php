<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Repositories\OAuthRepository;
use App\Services\DocumentService;
use App\Services\SignatureRequestService;
use App\Services\SignatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService         $documentService,
        protected OAuthRepository         $oauthRepository,
        protected SignatureRequestService $signatureRequestService
    )
    {
    }

    /**
     * @OA\Get(
     *     path="/documents",
     *     summary="Get a list of documents",
     *     tags={"Documents"},
     *     @OA\Response(
     *         response=200,
     *         description="Documents retrieved successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Document")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No documents found"
     *     ),
     *     security={
     *         {"passport": {}}
     *       }
     * )
     */
    public function index(): JsonResponse
    {
        $documents = $this->documentService->all();
        if ($documents->isEmpty()) {
            return response()->json([
                'success' => true,
                'statusCode' => 404,
                'message' => 'No documents found.',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'statusCode' => 200,
            'message' => 'Documents retrieved successfully.',
            'data' => $documents,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/documents",
     *     summary="Upload a new document",
     *     tags={"Documents"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/DocumentRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Document uploaded successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Document")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Document could not be uploaded"
     *     ),
     *     security={
     *         {"passport": {}}
     *       }
     * )
     */
    public function store(DocumentRequest $request): JsonResponse
    {
        $documentData = $request->validated();
        if ($documentData) {
            $document = $this->documentService->create($request->file('document'));
            if(!$document) {
                return response()->json([
                    'message' => 'Document could not be uploaded',
                ], 400);
            }
            return response()->json([
                'message' => 'Document uploaded successfully',
                'document' => $document,
                'filePath' => Storage::path($document->file_path),
            ], 201);
        }
        return response()->json([
            'message' => 'Document could not be uploaded',
        ], 400);
    }

    /**
     * @OA\Get(
     *     path="/documents/{uuid}",
     *     summary="Get a specific document",
     *     tags={"Documents"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the document"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Document")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document not found"
     *     ),
     *     security={
     *         {"passport": {}}
     *       }
     * )
     */
    public function show(string $uuid): JsonResponse
    {
        $document = $this->documentService->find($uuid);

        if ($document) {
            // Get the file path from storage
            $filePath = Storage::path($document->file_path);
            return response()->json([
                'success' => true,
                'statusCode' => 200,
                'message' => 'Document retrieved successfully',
                'filePath' => $filePath, // Path to the document for internal use (not publicly accessible)
            ]);
        }
        return response()->json([
            'success' => true,
            'statusCode' => 404,
            'message' => ' Document not found. Or you do not have access to this document.',
        ], 404);

    }


    /**
     * @OA\Delete(
     *     path="/documents/{uuid}",
     *     summary="Delete a document",
     *     tags={"Documents"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the document"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document not found"
     *     ),
     *     security={
     *         {"passport": {}}
     *       }
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $document = $this->documentService->find($id);
        if ($document) {
            // Get the file path from storage
            Storage::delete($document->file_path);
            return response()->json([
                'success' => true,
                'statusCode' => 200,
                'message' => 'Document deleted successfully',
            ]);
            $document->delete();
        }
        return response()->json([
            'success' => true,
            'statusCode' => 404,
            'message' => 'Document not found.',
        ], 404);
    }

    /**
     * @OA\Patch(
     *     path="/documents/{uuid}/status",
     *     summary="Update the status of a document",
     *     tags={"Documents"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the document"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"pending", "signed"},
     *                 description="The new status of the document"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document status updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Document")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document not found"
     *     ),
     *     security={
     *         {"passport": {}}
     *       }
     * )
     */
    public function updateStatus(Request $request, $uuid): JsonResponse
    {
        // Validate status input
        $validatedResponse = $request->validate([
            'status' => 'required|in:pending,signed',
        ]);

        if(!$validatedResponse) {
            return response()->json([
                'success' => true,
                'statusCode' => 400,
                'message' => 'Document status could not be updated',
            ], 400);
        }

        $document = $this->documentService->update($uuid, [
            'status' => $request->status,
        ]);
        if ($document) {
            return response()->json([
                'success' => true,
                'statusCode' => 200,
                'message' => 'Document status updated successfully',
                'document' => $document,
            ], 200);
        }
        return response()->json([
            'success' => true,
            'statusCode' => 404,
            'message' => 'Document not found.',
        ], 404);


    }

    /**
     * @OA\Get(
     *     path="/documents/{uuid}/download",
     *     summary="Download a document",
     *     tags={"Documents"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the document"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document downloaded successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Document")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document not found"
     *     ),
     *     security={
     *         {"passport": {}}
     *       }
     * )
     */
    public function download($uuid): StreamedResponse|JsonResponse
    {
        $document = $this->documentService->find($uuid);
        if (!$document) {
            return response()->json([
                'success' => true,
                'statusCode' => 404,
                'message' => 'Document not found.',
            ], 404);
        }
        return Storage::download($document->file_path);
    }


    /**
     * @OA\Post(
     *     path="/documents/{uuid}/send-signature-request",
     *     summary="Send a signature request for a document",
     *     tags={"Signature Requests"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="The ID of the document"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/SignatureRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Signature request sent successfully",
     *         @OA\JsonContent(ref="#/components/schemas/SignatureRequest")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="You do not own this document"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Signature request could not be sent"
     *     ),
     *     security={
     *         {"passport": {}}
     *       }
     * )
     */
    public function sendSignatureRequest(Request $request, $uuid): JsonResponse
    {
        // Validate the request input
        $response = $request->validate([
            'signer_id' => 'required|exists:users,id', // Ensure the signer is a valid user
        ]);

        if (!$response) {
            return response()->json([
                'success' => true,
                'statusCode' => 400,
                'message' => 'Signature request could not be sent',
            ], 400);
        }

        $document = $this->documentService->find($uuid);

        if (!$document) {
            return response()->json([
                'success' => true,
                'statusCode' => 404,
                'message' => 'Document not found.Or you do not have access to this document.',
            ], 404);
        }
        // Check if the current user is the document owner
        if ($document->user_id !== auth()->id()) {
            return response()->json(['message' => 'You do not own this document'], 403);
        }
        $signatureRequest = $this->signatureRequestService->create([
            'document_id' => $document->id,
            'requester_id' => auth()->id(),
            'signer_id' => $request->signer_id,
            'status' => 'pending',
        ]);

        if ($signatureRequest) {
            return response()->json([
                'success' => true,
                'statusCode' => 201,
                'message' => 'Signature request sent successfully',
                'signature_request' => $signatureRequest,
            ], 201);
        }
        return response()->json([
            'success' => true,
            'statusCode' => 400,
            'message' => 'Signature request could not be sent',
        ], 400);
    }


    /**
     * @OA\Patch(
     *     path="/documents/signature-requests/{id}/status",
     *     summary="Update signature request status",
     *     tags={"Signature Requests"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Signature request ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"pending", "signed"},
     *                 description="New status"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status updated",
     *         @OA\JsonContent(ref="#/components/schemas/SignatureRequest")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Request not found"
     *     ),
     *     security={
     *         {"passport": {}}
     *       }
     * )
     */
    public function updateSignatureRequestStatus(Request $request, $id): JsonResponse
    {
        // Validate the status input
        $request->validate([
            'status' => 'required|in:pending,signed', // Ensure the status is either 'pending' or 'signed'
        ]);

        // Find the signature request
        $signatureRequest = $this->signatureRequestService->find($id);

        // Check if the current user is the requester or signer
        if ($signatureRequest->requester_id !== auth()->id() && $signatureRequest->signer_id !== auth()->id()) {
            return response()->json(['message' => 'You are not authorized to update this signature request'], 403);
        }

        // Update the status of the signature request
        $signResponse = $this->signatureRequestService->update($id, [
            'status' => $request->status,
        ]);

        if ($signResponse) {
            return response()->json([
                'success' => true,
                'statusCode' => 200,
                'message' => 'Signature request status updated successfully',
            ], 200);
        }
        return response()->json([
            'success' => true,
            'statusCode' => 404,
            'message' => 'Signature not found.',
        ], 404);
    }

    /**
     * @OA\Post(
     *     path="/documents/{uuid}/add-signature",
     *     summary="Add a signature to a document",
     *     tags={"Signature Requests"},
     *     @OA\Parameter(
     *         name="documentId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Document ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Signature")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Document signed successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Signature")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Not authorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Signature error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Signature request not found"
     *     ),
     *     security={
     *         {"passport": {}}
     *       }
     * )
     */
    public function addSignature(Request $request, $uuid, SignatureService $signatureService): JsonResponse
    {
        // Validate the signature data
        $request->validate([
            'signature_data' => 'required|string',  // Ensure signature is a valid base64 string or JSON
        ]);

        // Find the document
        $document = $this->documentService->find($uuid);

        // Check if the current user is the signer
        $signatureRequest = $document->signatureRequests()->where('signer_id', auth()->id())->first();


        if (!$signatureRequest) {
            return response()->json(['message' => 'You are not authorized to sign this document.'], 403);
        }

        if ($signatureRequest->status !== 'pending') {
            return response()->json(['message' => 'This document is already signed or the request has expired.'], 400);
        }

        $signatureData = encrypt($request->signature_data);
        $signatureHash = hash('sha256', $request->signature_data);
        $signature = $signatureService->create([
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'signature_data' => $signatureData,
            'signature_hash' => $signatureHash,
            'signed_at' => now(),
        ]);

        if (!$signature) {
            return response()->json([
                'success' => true,
                'statusCode' => 400,
                'message' => 'Signature could not be added',
            ], 400);
        }

        $signResponse = $this->signatureRequestService->update($signatureRequest->id, [
            'status' => 'signed'
        ]);

        if ($signResponse) {
            return response()->json([
                'success' => true,
                'statusCode' => 200,
                'message' => 'Document signed successfully',
            ], 200);
        }
        return response()->json([
            'success' => true,
            'statusCode' => 404,
            'message' => 'Signature not found.',
        ], 404);
    }

    /**
     * @OA\Get(
     *     path="/documents/{uuid}/signed",
     *     summary="Show signed document",
     *     description="Returns a signed document along with its signature data if available.",
     *     tags={"Signature Requests"},
     *     @OA\Parameter(
     *         name="documentId",
     *         in="path",
     *         description="The ID of the document to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Signed document retrieved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="document", type="string", description="URL to the document file"),
     *             @OA\Property(property="signature", type="string", description="The signature data"),
     *             @OA\Property(property="signed_at", type="string", format="date-time", description="The time when the document was signed"),
     *             @OA\Property(property="signer", type="string", description="Name of the signer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Document not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No signature found or signature integrity is compromised"
     *     ),
     *     security={
     *          {"passport": {}}
     *        }
     * )
     */
    public function showSignedDocument($uuid)
    {
        // Find the document
        $document = $this->documentService->find($uuid);

        if (!$document) {
            return response()->json(['message' => 'Document not found.'], 404);
        }

        // Check if the document has a signature
        $signature = $document->signatures()->latest()->first();

        if (!$signature) {
            return response()->json(['message' => 'No signature found for this document.'], 400);
        }
        // Decrypt the signature data
        $signatureData = decrypt($signature->signature_data);

        // Verify the signature integrity by comparing the hash
        $signatureHash = hash('sha256', $signatureData); // Hash the stored signature data

        if ($signature->signature_hash !== $signatureHash) {
            return response()->json(['message' => 'Signature integrity is compromised.'], 400);
        }

        // Return document file URL and signature data (if needed)
        return response()->json([
            'success' => true,
            'statusCode' => 200,
            'message' => 'Document signed successfully',
            'data' => [
                'document' => Storage::url($document->file_path),
                'signature' => $signatureData,
                'signed_at' => $signature->signed_at,
                'signer' => $signature->signer->name,
            ],
        ], 200);
    }
}
