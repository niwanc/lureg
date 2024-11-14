<?php

namespace Tests\Feature\Document;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DocumentStoreTest extends TestCase
{
    use DatabaseTransactions;
    protected User|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model $user;
    protected function setUp(): void
    {
        parent::setUp();
        // Create a user in the database
        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }
    /**
     * Test document upload success.
     *
     * @return void
     */
    public function test_document_upload_success()
    {

        // Simulate a document file upload
        $file = UploadedFile::fake()->createWithContent('document.pdf', "%PDF-1.4\n" .'This is the content of the file.'); // 100 KB file

        // Make the POST request to store the document
        $response = $this->postJson('/api/documents', [
            'document' => $file,
        ]);

        // Assert the response status and structure
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'document',
                'filePath',
            ]);

        // Assert that the file was stored in the storage
       // Storage::fake('public')->assertExists('documents/' . $file->hashName());

        // Optionally, you can check if the document is created in the database
        $this->assertDatabaseHas('documents', [
            'id' => $response->json('document.id'),
        ]);
    }

    /**
     * Test document upload failure (e.g., invalid file).
     *
     * @return void
     */
    public function test_document_upload_failure()
    {
        // Simulate an invalid file (not a document)
        $file = UploadedFile::fake()->image('invalid-image.jpg', 100, 100);

        // Make the POST request with invalid file
        $response = $this->postJson('/api/documents', [
            'document' => $file,
        ]);

        // Assert the response status and message
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The document field must be a file of type: pdf.',
            ]);
    }

    /**
     * Test document upload without file.
     *
     * @return void
     */
    public function test_document_upload_no_file()
    {
        // Make the POST request without sending a file
        $response = $this->postJson('/api/documents', []);

        // Assert the response status and message
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The document field is required.',
            ]);
    }
}
