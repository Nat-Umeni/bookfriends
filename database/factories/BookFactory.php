<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\User;
use App\Models\BookUser;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'author' => fake()->name(),
        ];
    }

    public function withUserStatus(?User $user = null, ?string $status = null): static
    {
        return $this->afterCreating(function ($book) use ($user, $status) {
            $user = $user ?: User::factory()->create();

            $status = $status ?: collect(BookUser::allowedStatuses())->random();

            $user->books()->attach($book->id, ['status' => $status]);
        });
    }
}
