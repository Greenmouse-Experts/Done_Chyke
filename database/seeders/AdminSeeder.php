<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'account_type' => 'Administrator',
            'name' => 'Admin',
            'email' => 'admin@donandchyke.com.ng',
            'email_verified_at' => now(),
            'access' => true,
            'password' => bcrypt('Password'),
            'role' => 'Master',
        ]);
    }
}
