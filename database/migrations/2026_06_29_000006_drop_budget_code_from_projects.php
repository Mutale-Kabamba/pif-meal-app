<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        if (DB::getDriverName() === 'sqlite') {
            // SQLite cannot drop columns with unique indexes directly; rebuild the table.
            DB::statement('CREATE TABLE "projects_new" (
                "id"                                INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                "name"                              VARCHAR(255) NOT NULL,
                "programme_type"                    VARCHAR(255) NOT NULL DEFAULT \'football\',
                "daily_meal_limit_per_beneficiary"  INTEGER NOT NULL DEFAULT 1,
                "is_active"                         TINYINT(1) NOT NULL DEFAULT 1,
                "description"                       TEXT NULL,
                "created_at"                        DATETIME NULL,
                "updated_at"                        DATETIME NULL
            )');

            DB::statement('INSERT INTO "projects_new"
                SELECT "id", "name", "programme_type", "daily_meal_limit_per_beneficiary",
                       "is_active", "description", "created_at", "updated_at"
                FROM "projects"');

            DB::statement('DROP TABLE "projects"');
            DB::statement('ALTER TABLE "projects_new" RENAME TO "projects"');
        } else {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropUnique(['budget_code']);
                $table->dropColumn('budget_code');
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('budget_code')->unique()->after('name');
        });
    }
};
