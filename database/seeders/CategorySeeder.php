<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Programming',     'icon' => 'bi-code-slash',       'description' => 'Software development, languages, frameworks and engineering practices.'],
            ['name' => 'Data Science',    'icon' => 'bi-graph-up',         'description' => 'Statistics, machine learning, analytics and data engineering.'],
            ['name' => 'Mathematics',     'icon' => 'bi-calculator',       'description' => 'Calculus, linear algebra, discrete math and more.'],
            ['name' => 'Business',        'icon' => 'bi-briefcase',        'description' => 'Entrepreneurship, marketing, finance and management.'],
            ['name' => 'Languages',       'icon' => 'bi-translate',        'description' => 'English, Urdu literacy and second-language acquisition.'],
            ['name' => 'Design',          'icon' => 'bi-palette',          'description' => 'UI/UX, graphic design and visual communication.'],
        ];

        foreach ($categories as $i => $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                array_merge($cat, ['sort_order' => $i + 1])
            );
        }
    }
}
