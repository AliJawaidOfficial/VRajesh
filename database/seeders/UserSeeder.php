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
            'name' => 'Admin',
            'email' => 'alijawaidofficial.pk@gmail.com',
            'email_verified_at' => now(),
            'meta_email' => 'alijawaidofficial.pk@gmail.com',
            'linkedin_email' => 'alijawaidofficial.pk@gmail.com',
            'password' => Hash::make('admin'),
        ]);
    }
}
