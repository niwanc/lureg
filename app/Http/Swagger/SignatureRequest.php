<?php

namespace App\Http\Swagger;
/**
 * @OA\Schema(
 *     schema="SignatureRequest",
 *     type="object",
 *     title="Signature Request",
 *     description="Request body for signature operations",
 *     required={"document_id", "signer_id"},
 *     @OA\Property(
 *         property="document_id",
 *         type="integer",
 *         description="The ID of the document to be signed"
 *     ),
 *     @OA\Property(
 *         property="signer_id",
 *         type="integer",
 *         description="The ID of the user who will sign the document"
 *     )
 * )
 */
class SignatureRequest
{

}
