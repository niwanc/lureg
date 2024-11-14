<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/refresh', [AuthController::class, 'refreshToken']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    //Document routes
    Route::apiResource('documents', DocumentController::class)->except('store');
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::patch('/documents/{uuid}/status', [DocumentController::class, 'updateStatus']);
    Route::get('/documents/{uuid}/download', [DocumentController::class, 'download']);
    //Signature request routes
    Route::post('/documents/{uuid}/signature-request', [DocumentController::class, 'sendSignatureRequest'])
        ->name('signature-request.send');
    Route::patch('/signature-requests/{signatureRequestId}/status', [DocumentController::class, 'updateSignatureRequestStatus']);
    Route::post('/documents/{uuid}/add-signature', [DocumentController::class, 'addSignature'])
        ->name('document.addSignature');
    Route::get('/documents/{uuid}/signed', [DocumentController::class, 'showSignedDocument'])
        ->name('document.showSignedDocument');
});

