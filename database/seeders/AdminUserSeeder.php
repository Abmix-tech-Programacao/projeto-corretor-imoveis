<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@corretorprime.com.br'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin@123456'),
                'is_admin' => true,
            ]
        );
    }
}
