<?php

namespace Database\Factories;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Medicine::class;

    public function definition(): array
    {
        $purchasePrice = $this->faker->randomFloat(2, 1000, 50000);
        $sellingPrice = $purchasePrice + ($purchasePrice * 0.2);

        return [
            'code' => $this->faker->unique()->numerify('MED-#####'),
            'name' => $this->faker->words(3, true),
            'category' => $this->faker->randomElement(['Tablet', 'Sirup', 'Kapsul', 'Injeksi', 'Salep']),
            'unit' => $this->faker->randomElement(['Botol', 'Strip', 'Box', 'Tube', 'Vial']),
            'purchase_price' => $purchasePrice,
            'selling_price' => $sellingPrice,
            'stock' => $this->faker->numberBetween(0, 1000),
            'minimum_stock' => $this->faker->numberBetween(10, 50),
            'expired_date' => $this->faker->dateTimeBetween('-1 month', '+2 years')->format('Y-m-d'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
