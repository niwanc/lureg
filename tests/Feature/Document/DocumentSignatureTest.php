<?php

namespace Tests\Feature\Document;

use App\Models\Document;
use App\Models\SignatureRequest;
use App\Models\User;
use App\Services\SignatureRequestService;
use App\Services\SignatureService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DocumentSignatureTest extends TestCase
{
    use DatabaseTransactions;

    public function test_add_signature_successfully()
    {
        // Create a user and a document
        $user = User::factory()->create();
        $document = Document::factory()->create(['user_id' => $user->id]);

        // Create a signature request
        $signatureRequest = SignatureRequest::factory()->create([
            'document_id' => $document->id,
            'signer_id' => $user->id,
            'status' => 'pending',
        ]);

        Passport::actingAs($user);

        // Send a valid request
        $response = $this->postJson(route('document.addSignature', $document->uuid), [
            'signature_data' => 'fake_signature_data',
        ]);

        // Assertions
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'statusCode' => 200,
                'message' => 'Document signed successfully',
            ]);
    }

    public function test_add_signature_unauthorized()
    {
        // Create two users and a document
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $document = Document::factory()->create(['user_id' => $user->id]);

        // Create a signature request for another user
        $signatureRequest = SignatureRequest::factory()->create([
            'document_id' => $document->id,
            'signer_id' => $otherUser->id,
            'status' => 'pending',
        ]);

        // Simulate the user being authenticated (but not the signer)
        Passport::actingAs($user);

        // Send a request for unauthorized signer
        $response = $this->postJson(route('document.addSignature', $document->uuid), [
            'signature_data' => 'fake_signature_data',
        ]);

        // Assertions
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You are not authorized to sign this document.',
            ]);
    }

    public function test_add_signature_already_signed()
    {
        // Create a user and a document
        $user = User::factory()->create();
        $document = Document::factory()->create(['user_id' => $user->id]);

        // Create a signature request and set its status to signed
        $signatureRequest = SignatureRequest::factory()->create([
            'document_id' => $document->id,
            'signer_id' => $user->id,
            'status' => 'signed',
        ]);

        // Simulate the user being authenticated
        Passport::actingAs($user);

        // Send a request to sign the document that is already signed
        $response = $this->json('POST', route('document.addSignature', ['uuid' => $document->uuid]), [
            'signature_data' => 'fake_signature_data',
        ]);

        // Assertions
        $response->assertStatus(400)
            ->assertJson([
                'message' => 'This document is already signed or the request has expired.',
            ]);
    }

    public function test_add_signature_invalid_signature_data()
    {
        // Create a user and a document
        $user = User::factory()->create();
        $document = Document::factory()->create(['user_id' => $user->id]);

        // Create a signature request
        $signatureRequest = SignatureRequest::factory()->create([
            'document_id' => $document->id,
            'signer_id' => $user->id,
            'status' => 'pending',
        ]);

        // Simulate the user being authenticated
        Passport::actingAs($user);

        // Send a request with invalid signature data (empty string)
        $response = $this->json('POST', route('document.addSignature', ['uuid' => $document->uuid]), [
            'signature_data' => '', // Invalid data
        ]);

        // Assertions
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The signature data field is required.',
            ]);
    }
}
