<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\penjualan;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Penjualan>
 */
class PenjualanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = penjualan::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->value('id'),
            'total_pembayaran' => 0, // akan diupdate di seeder
            'metode_pembayaran' => $this->faker->randomElement([
                'CASH', 'TRANSFER',  'QRIS'
    ]),
    'status' => $this->faker->randomElement(['OPEN', 'COMPLETED']),
];
    }
}
