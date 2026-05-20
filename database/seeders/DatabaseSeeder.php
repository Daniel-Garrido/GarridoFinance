<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'daniel',
            'email' => 'danielarcangelgh@gmail.com',
            'password' => bcrypt('12345678'),
        ]);

        // Crear 5 cuentas dinámicas para ese usuario
        Account::factory()
            ->count(5)
            ->create([
                'user_id' => $user->id,
            ]);
    }
}