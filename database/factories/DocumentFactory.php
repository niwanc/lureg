<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class DocumentFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid'    => Str::uuid(),
            'user_id' => User::factory(), // Creating a random user using User factory
            'file_path' => 'documents/' . Str::random(10) . '.pdf', // Sample file path, you can change logic as needed
            'status' => $this->faker->randomElement(['pending', 'signed']), // Sample status
        ];
    }

}
