<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\SignatureRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SignatureRequest>
 */
class SignatureRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'document_id' => Document::factory(), // Creates a related document
            'signer_id' => User::factory(),
            'requester_id' => User::factory(),
            'status' => $this->faker->randomElement(['pending', 'signed']), // Random status
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
