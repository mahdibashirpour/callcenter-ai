<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Organization;
use App\Support\Seeding\DemoCatalog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $phone = '09'.fake()->numerify('#########');

        return [
            'organization_id' => Organization::factory(),
            'normalized_phone' => DemoCatalog::normalizePhone($phone),
            'phone_number' => $phone,
            'name' => fake()->company(),
            'company_name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'job_title' => fake()->randomElement(['مدیر خرید', 'مسئول فنی', 'مالک']),
            'identity_confidence' => fake()->randomFloat(2, 0.5, 0.95),
            'purchase_intent' => fake()->randomElement(['بالا', 'متوسط', 'پایین']),
            'conversation_trend' => fake()->randomElement(['improving', 'stable', 'declining']),
            'total_calls' => 0,
            'total_answered_calls' => 0,
        ];
    }
}
