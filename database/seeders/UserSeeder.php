<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Ali',
            'last_name' => 'Jawaid',
            'email' => 'alijawaidofficial.pk@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('aj12345'),
        ]);
    }
}
