<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $catInformatika = Category::where('name', 'Informatika & Komputer')->first();
        $catSI = Category::where('name', 'Sistem Informasi')->first();
        $catSains = Category::where('name', 'Sains & Teknologi')->first();

        $books = [
            [
                'title' => 'Pemrograman Web dengan Laravel 10',
                'author' => 'Ahmad Subagja',
                'published_year' => 2023,
                'description' => 'Buku ini membahas secara mendalam tentang pengembangan aplikasi web menggunakan framework Laravel 10. Mulai dari instalasi, routing, Eloquent ORM, Blade templating, hingga deployment ke server produksi. Cocok untuk pemula maupun yang sudah berpengalaman.',
                'category_id' => $catInformatika?->id ?? 1,
                'stock' => 5,
                'cover_image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'title' => 'Struktur Data dan Algoritma Modern',
                'author' => 'Budi Raharjo',
                'published_year' => 2022,
                'description' => 'Pengenalan komprehensif tentang struktur data fundamental dan algoritma yang efisien. Topik mencakup array, linked list, stack, queue, tree, graph, dan berbagai teknik sorting dan searching yang diimplementasikan dengan pendekatan modern.',
                'category_id' => $catInformatika?->id ?? 1,
                'stock' => 3,
                'cover_image' => 'https://images.unsplash.com/photo-1516116211223-4c599705f381?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'title' => 'Analisis dan Perancangan Sistem Informasi',
                'author' => 'Dr. Eko Prasetyo',
                'published_year' => 2021,
                'description' => 'Panduan lengkap untuk menganalisis kebutuhan dan merancang sistem informasi yang efektif. Membahas metodologi SDLC, pembuatan DFD, ERD, use case diagram, dan berbagai teknik perancangan basis data yang digunakan dalam industri.',
                'category_id' => $catSI?->id ?? 2,
                'stock' => 2,
                'cover_image' => 'https://images.unsplash.com/photo-1532012197267-da84d127e765?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'title' => 'Dasar-Dasar Kecerdasan Buatan',
                'author' => 'Siti Nurhaliza',
                'published_year' => 2024,
                'description' => 'Pengantar dunia kecerdasan buatan (Artificial Intelligence) yang mencakup konsep machine learning, deep learning, natural language processing, dan computer vision. Dilengkapi dengan studi kasus nyata dan implementasi menggunakan Python.',
                'category_id' => $catSains?->id ?? 3,
                'stock' => 0,
                'cover_image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=600&q=80',
            ],
            [
                'title' => 'Manajemen Proyek Perangkat Lunak',
                'author' => 'Hendra Wijaya',
                'published_year' => 2022,
                'description' => 'Buku ini membahas prinsip dan praktik manajemen proyek perangkat lunak secara profesional. Topik meliputi perencanaan proyek, estimasi biaya dan waktu, manajemen risiko, metodologi Agile & Scrum, serta cara memimpin tim pengembang secara efektif.',
                'category_id' => $catSI?->id ?? 2,
                'stock' => 4,
                'cover_image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=600&q=80',
            ],
        ];

        foreach ($books as $data) {
            Book::updateOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}
