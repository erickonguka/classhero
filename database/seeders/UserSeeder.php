<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@classhero.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'bio' => 'Platform administrator',
            'points' => 0,
        ]);

        // Teacher users
        $teachers = [
            ['name' => 'John Smith', 'email' => 'john@classhero.com', 'bio' => 'Full-stack web developer with 10+ years experience'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah@classhero.com', 'bio' => 'UI/UX designer and frontend specialist'],
            ['name' => 'Mike Chen', 'email' => 'mike@classhero.com', 'bio' => 'Data scientist and machine learning expert'],
            ['name' => 'Emily Davis', 'email' => 'emily@classhero.com', 'bio' => 'Mobile app developer and iOS specialist'],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'bio' => $teacher['bio'],
                'points' => 0,
            ]);
        }

        // Learner users
        $learners = [
            ['name' => 'Alice Wilson', 'email' => 'alice@example.com'],
            ['name' => 'Bob Brown', 'email' => 'bob@example.com'],
            ['name' => 'Carol White', 'email' => 'carol@example.com'],
            ['name' => 'David Lee', 'email' => 'david@example.com'],
        ];

        foreach ($learners as $learner) {
            User::create([
                'name' => $learner['name'],
                'email' => $learner['email'],
                'password' => Hash::make('password'),
                'role' => 'learner',
                'points' => rand(0, 1000),
            ]);
        }
    }
}