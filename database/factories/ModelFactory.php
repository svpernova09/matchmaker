<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    $gender = ['male', 'female'];

    return [
        'username' => $faker->unique()->username,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'gender' => $gender[rand(0, 1)],
        'tagline' => $faker->sentence,
        'profile' => $faker->paragraph,
        'remember_token' => str_random(10),
    ];
});
