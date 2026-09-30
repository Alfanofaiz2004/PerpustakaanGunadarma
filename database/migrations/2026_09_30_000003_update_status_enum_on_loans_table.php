<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // For SQLite, recreate table without the restrictive CHECK constraint so 'ditolak' is allowed
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            DB::statement('
                CREATE TABLE "loans_temp" (
                    "id" integer primary key autoincrement not null,
                    "user_id" integer not null,
                    "book_id" integer not null,
                    "loan_date" date not null,
                    "due_date" date,
                    "return_date" date,
                    "status" varchar check ("status" in (\'pending\', \'dipinjam\', \'return_requested\', \'dikembalikan\', \'ditolak\')) not null default \'pending\',
                    "created_at" datetime,
                    "updated_at" datetime,
                    "rejection_note" varchar,
                    foreign key("user_id") references "users"("id") on delete cascade,
                    foreign key("book_id") references "books"("id") on delete cascade
                );
            ');

            DB::statement('
                INSERT INTO "loans_temp" ("id", "user_id", "book_id", "loan_date", "due_date", "return_date", "status", "created_at", "updated_at", "rejection_note")
                SELECT "id", "user_id", "book_id", "loan_date", "due_date", "return_date", "status", "created_at", "updated_at", "rejection_note" FROM "loans";
            ');

            DB::statement('DROP TABLE "loans";');
            DB::statement('ALTER TABLE "loans_temp" RENAME TO "loans";');

            DB::statement('PRAGMA foreign_keys=ON;');
        } else {
            Schema::table('loans', function (Blueprint $table) {
                $table->string('status', 30)->default('pending')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            DB::statement('
                CREATE TABLE "loans_temp" (
                    "id" integer primary key autoincrement not null,
                    "user_id" integer not null,
                    "book_id" integer not null,
                    "loan_date" date not null,
                    "due_date" date,
                    "return_date" date,
                    "status" varchar check ("status" in (\'pending\', \'dipinjam\', \'return_requested\', \'dikembalikan\')) not null default \'pending\',
                    "created_at" datetime,
                    "updated_at" datetime,
                    "rejection_note" varchar,
                    foreign key("user_id") references "users"("id") on delete cascade,
                    foreign key("book_id") references "books"("id") on delete cascade
                );
            ');

            DB::statement('
                INSERT INTO "loans_temp" ("id", "user_id", "book_id", "loan_date", "due_date", "return_date", "status", "created_at", "updated_at", "rejection_note")
                SELECT "id", "user_id", "book_id", "loan_date", "due_date", "return_date", "status", "created_at", "updated_at", "rejection_note" FROM "loans" WHERE "status" != \'ditolak\';
            ');

            DB::statement('DROP TABLE "loans";');
            DB::statement('ALTER TABLE "loans_temp" RENAME TO "loans";');

            DB::statement('PRAGMA foreign_keys=ON;');
        }
    }
};
