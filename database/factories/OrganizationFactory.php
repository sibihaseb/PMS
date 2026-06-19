<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
        ];
    }

    public function pro(): static
    {
        return $this->afterCreating(function (Organization $organization): void {
            $organization->subscriptions()->create([
                'type' => 'default',
                'stripe_id' => 'sub_'.fake()->unique()->uuid(),
                'stripe_status' => 'active',
                'stripe_price' => config('cashier.pro_price_id', 'price_test_pro'),
            ]);
        });
    }

    public function team(): static
    {
        return $this->afterCreating(function (Organization $organization): void {
            $organization->subscriptions()->create([
                'type' => 'default',
                'stripe_id' => 'sub_'.fake()->unique()->uuid(),
                'stripe_status' => 'active',
                'stripe_price' => config('cashier.team_price_id', 'price_test_team'),
            ]);
        });
    }
}
