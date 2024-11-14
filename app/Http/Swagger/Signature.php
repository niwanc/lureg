<?php

namespace App\Http\Swagger;
/**
 * @OA\Schema(
 *     schema="Signature",
 *     type="object",
 *     title="Signature",
 *     description="Signature model",
 *     required={"document_id", "user_id", "signature_data", "signed_at"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The unique identifier of the signature"
 *     ),
 *     @OA\Property(
 *         property="document_id",
 *         type="integer",
 *         description="The ID of the document being signed"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="The ID of the user who signed the document"
 *     ),
 *     @OA\Property(
 *         property="signature_data",
 *         type="string",
 *         description="The signature data (base64 string or JSON)"
 *     ),
 *     @OA\Property(
 *         property="signed_at",
 *         type="string",
 *         format="date-time",
 *         description="The timestamp when the document was signed"
 *     )
 * )
 */
class Signature
{

}
