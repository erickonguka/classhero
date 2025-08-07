<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Web Development', 'description' => 'Learn modern web development technologies', 'color' => '#3B82F6'],
            ['name' => 'Mobile Development', 'description' => 'Build mobile applications for iOS and Android', 'color' => '#10B981'],
            ['name' => 'Data Science', 'description' => 'Analyze data and build machine learning models', 'color' => '#8B5CF6'],
            ['name' => 'Design', 'description' => 'UI/UX design and graphic design courses', 'color' => '#F59E0B'],
            ['name' => 'Business', 'description' => 'Business skills and entrepreneurship', 'color' => '#EF4444'],
            ['name' => 'Marketing', 'description' => 'Digital marketing and growth strategies', 'color' => '#EC4899'],
            ['name' => 'Photography', 'description' => 'Photography techniques and editing', 'color' => '#6B7280'],
            ['name' => 'Music', 'description' => 'Music theory and instrument lessons', 'color' => '#84CC16'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'color' => $category['color'],
                'is_active' => true,
            ]);
        }
    }
}