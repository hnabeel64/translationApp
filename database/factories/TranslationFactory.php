<?php

namespace Database\Factories;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Translation>
 */
class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Translation::class;
    public function definition() {
        return [
            'locale_id' => Locale::inRandomOrder()->first()->id ?? Locale::factory()->create()->id,
            'key' => $this->faker->word,
            'content' => $this->faker->sentence,
            'tags' => $this->faker->randomElement(['mobile', 'desktop', 'web'])
        ];
    }
}
