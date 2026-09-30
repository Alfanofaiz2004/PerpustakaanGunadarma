<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@gunadarma.ac.id']);
        $admin->name = 'Administrator Perpustakaan';
        $admin->password = Hash::make('password');
        $admin->role = 'admin';
        $admin->save();

        $student = User::firstOrNew(['email' => 'mahasiswa@gunadarma.ac.id']);
        $student->name = 'Mahasiswa Gunadarma (Demo)';
        $student->npm = '50421001';
        $student->password = Hash::make('password');
        $student->role = 'peminjam';
        $student->save();
    }
}
