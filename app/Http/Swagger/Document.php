<?php

namespace App\Http\Swagger;

/**
 * @OA\Schema(
 *     schema="Document",
 *     type="object",
 *     title="Document",
 *     description="Document object",
 *     required={"id", "name", "url"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="The document ID"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The document name"
 *     ),
 *     @OA\Property(
 *         property="filePath",
 *         type="string",
 *         description="The document URL"
 *     )
 * )
 */
class Document
{
  // This class is used only for OpenAPI documentation purposes
}
