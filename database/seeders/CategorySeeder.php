<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Informatika & Komputer',
            'Sistem Informasi',
            'Sains & Teknologi',
            'Ekonomi & Bisnis',
            'Sastra & Bahasa',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(['name' => $name]);
        }
    }
}
