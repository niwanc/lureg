<?php

namespace Tests\Feature\Document;

use App\Models\Document;
use App\Models\User;
use App\Services\SignatureRequestService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class SignatureRequestTest extends TestCase
{
    use DatabaseTransactions;

    public function test_sends_signature_request_for_valid_document_and_user()
    {
        // Create a user (the owner of the document) and a signer user
        $user = User::factory()->create();
        $signer = User::factory()->create();

        // Create a document owned by the user
        $document = Document::factory()->create([
            'user_id' => $user->id, // The owner of the document
        ]);

        // Act as the owner user
        Passport::actingAs($user);

        // Send the POST request to create a signature request
        $response = $this->postJson(route('signature-request.send', $document->uuid), [
            'document_id' => $document->id,
            'signer_id' => $signer->id,
        ]);

        // Assert that the response is a success
        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Signature request sent successfully',
        ]);

        // Assert that the signature request has been saved in the database
        $this->assertDatabaseHas('signature_requests', [
            'document_id' => $document->id,
            'signer_id' => $signer->id,
            'status' => 'pending',
            'requester_id' => $user->id,
        ]);
    }


    public function test_returns_error_if_document_is_not_owned_by_user()
    {
        // Create two users, one being the owner and another being the requester
        $user = User::factory()->create();  // Not the owner
        $anotherUser = User::factory()->create();  // The document's owner
        $signer = User::factory()->create();

        // Create a document owned by another user
        $document = Document::factory()->create([
            'user_id' => $anotherUser->id,  // Different owner
        ]);

        // Act as the non-owner user
        Passport::actingAs($user);

        // Send the POST request to create a signature request
        $response = $this->postJson(route('signature-request.send', $document->uuid), [
            'signer_id' => $signer->id,
        ]);

        // Assert that the response is a 403 forbidden
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'You do not own this document',
        ]);
    }

    public function test_returns_error_if_signature_request_creation_fails()
    {
        // Create a user and document
        $user = User::factory()->create();
        $signer = User::factory()->create();
        $document = Document::factory()->create([
            'user_id' => $user->id,
        ]);

        // Mock the service to simulate failure in creating a signature request
        $this->mock(SignatureRequestService::class, function ($mock) {
            $mock->shouldReceive('create')->andReturnNull();  // Simulate failure
        });

        Passport::actingAs($user);

        // Send the POST request to create a signature request
        $response = $this->postJson(route('signature-request.send', $document->uuid), [
            'signer_id' => $signer->id,
        ]);

        // Assert that the response returns a failure message
        $response->assertStatus(400);
        $response->assertJson([
            'success' => true,
            'message' => 'Signature request could not be sent',
        ]);
    }
}
