<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('npm', 30)->nullable()->unique()->after('name');
        });

        // Set default NPM for existing demo users if present
        DB::table('users')->where('email', 'mahasiswa@gunadarma.ac.id')->update(['npm' => '50421001']);
        DB::table('users')->where('email', 'alfanofaiz@gmail.com')->update(['npm' => '50421002']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('npm');
        });
    }
};
