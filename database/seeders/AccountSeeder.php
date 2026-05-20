<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $user = User::create([
                'name' => 'Dani Garrido',
                'email' => 'dani@test.com',
                'password' => bcrypt('password'),
            ]);
        }

        $accounts = [
            [
                'name' => 'Efectivo',
                'type' => 'cash',
                'is_active' => true,
            ],
            [
                'name' => 'BBVA',
                'type' => 'bank',
                'is_active' => true,
            ],
            [
                'name' => 'Nu',
                'type' => 'card',
                'is_active' => true,
            ],
            [
                'name' => 'Ahorro',
                'type' => 'saving',
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $account) {
            Account::create([
                'user_id' => $user->id,
                'name' => $account['name'],
                'type' => $account['type'],
                'is_active' => $account['is_active'],
            ]);
        }
    }
}