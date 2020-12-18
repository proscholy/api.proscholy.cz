<?php

namespace Database\Factories;

use App\SongLyric;
use App\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

class SongLyricFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SongLyric::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'song_id' => Song::factory(),
            'name' => function (array $attributes) {
                return Song::find($attributes['song_id'])->name;
            },
            'lyrics' => $this->faker->text,
        ];
    }
}
