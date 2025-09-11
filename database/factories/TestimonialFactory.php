<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $testimonials = [
            'Revamp365 has completely transformed my real estate investing. The platform makes it so easy to find and analyze deals that I was missing before.',
            'This app is a game-changer for property investors. The automated offer system saves me hours every week.',
            'I love how user-friendly the interface is. Even as a beginner, I can quickly find properties that match my investment criteria.',
            'The property analysis tools are incredible. I can run numbers on multiple deals simultaneously and make informed decisions quickly.',
            'Revamp365 helped me find my first successful flip. The deal would have been impossible to spot without their advanced filters.',
            'As a wholesaler, this platform has been invaluable for connecting with buyers and finding off-market opportunities.',
            'The customer support is outstanding. The team walked me through every feature and helped me optimize my workflow.',
            'This software pays for itself. I\'ve found deals worth six figures that I would have missed using traditional methods.',
            'The MLS integration is seamless. I can search multiple markets and get instant notifications on new listings.',
            'Revamp365 is now my go-to tool for all real estate analysis. It\'s like having a full-time research assistant.'
        ];

        return [
            'description' => $this->faker->randomElement($testimonials),
            'email' => $this->faker->email(),
            'name' => $this->faker->name(),
            'profile_image' => $this->faker->optional(0.7)->imageUrl(100, 100, 'people'),
            'published_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'rate' => $this->faker->randomElement([4, 5]), // Mostly positive ratings
            'title' => $this->faker->optional()->sentence(3),
        ];
    }

    /**
     * Indicate that the testimonial is highly rated.
     */
    public function highlyRated(): static
    {
        return $this->state(fn (array $attributes) => [
            'rate' => 5,
        ]);
    }

    /**
     * Indicate that the testimonial is recent.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the testimonial has a profile image.
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'profile_image' => $this->faker->imageUrl(100, 100, 'people'),
        ]);
    }
}
