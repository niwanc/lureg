<?php

namespace App\Http\Swagger;

/**
 * @OA\Schema(
 *     schema="DocumentRequest",
 *     type="object",
 *     title="Document Request",
 *     description="Request body for document operations",
 *     required={"document"},
 *     @OA\Property(
 *         property="document",
 *         type="string",
 *         format="binary",
 *         description="The document file to be uploaded"
 *     )
 * )
 */
class DocumentRequest
{
  // This class is used only for OpenAPI documentation purposes
}
