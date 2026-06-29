<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('beneficiary_project'); // clean up any orphaned table from a prior failed run

        // Many-to-many: a beneficiary can belong to a football project AND a literacy project
        Schema::create('beneficiary_project', function (Blueprint $table) {
            $table->foreignId('beneficiary_id')->constrained('beneficiaries')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->primary(['beneficiary_id', 'project_id']);
            $table->timestamps();
        });

        // Migrate existing single project_id → pivot rows (driver-agnostic)
        $now = now()->toDateTimeString();
        DB::table('beneficiaries')
            ->whereNotNull('project_id')
            ->select(['id', 'project_id'])
            ->orderBy('id')
            ->chunk(500, function ($rows) use ($now) {
                $insert = $rows->map(fn ($r) => [
                    'beneficiary_id' => $r->id,
                    'project_id'     => $r->project_id,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ])->all();

                DB::table('beneficiary_project')->insertOrIgnore($insert);
            });

        // Add team assignment for football beneficiaries (guard against re-run)
        if (!Schema::hasColumn('beneficiaries', 'team_id')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $table->foreignId('team_id')->nullable()->after('qr_token')->constrained('teams')->nullOnDelete();
            });
        }

        // Drop the old columns now replaced by the pivot / team relationship.
        // SQLite cannot drop FK-constrained columns via ALTER TABLE, so we rebuild the table.
        Schema::disableForeignKeyConstraints();

        if (DB::getDriverName() === 'sqlite') {
            // Rebuild beneficiaries without project_id / literacy_enrolled
            DB::statement('
                CREATE TABLE "beneficiaries_new" (
                    "id"         integer primary key autoincrement not null,
                    "name"       varchar not null,
                    "shortcode"  varchar not null,
                    "qr_token"   varchar not null,
                    "is_active"  tinyint(1) not null default 1,
                    "team_id"    integer references "teams"("id") on delete set null,
                    "created_at" datetime,
                    "updated_at" datetime
                )
            ');

            $cols = '"id","name","shortcode","qr_token","is_active","team_id","created_at","updated_at"';
            DB::statement("INSERT INTO \"beneficiaries_new\" ({$cols}) SELECT {$cols} FROM \"beneficiaries\"");
            DB::statement('DROP TABLE "beneficiaries"');
            DB::statement('ALTER TABLE "beneficiaries_new" RENAME TO "beneficiaries"');

            // Restore indexes
            DB::statement('CREATE UNIQUE INDEX "beneficiaries_shortcode_unique" ON "beneficiaries" ("shortcode")');
            DB::statement('CREATE UNIQUE INDEX "beneficiaries_qr_token_unique"  ON "beneficiaries" ("qr_token")');
        } else {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $table->dropForeign(['project_id']);
                $table->dropColumn(['project_id', 'literacy_enrolled']);
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('id')->constrained('projects')->nullOnDelete();
            $table->boolean('literacy_enrolled')->default(false)->after('is_active');
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['team_id']);
            }
            $table->dropColumn('team_id');
        });

        // Restore single project_id from pivot (first row per beneficiary)
        DB::table('beneficiary_project')
            ->select(['beneficiary_id', 'project_id'])
            ->orderBy('beneficiary_id')
            ->chunk(500, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('beneficiaries')
                        ->where('id', $row->beneficiary_id)
                        ->whereNull('project_id')
                        ->update(['project_id' => $row->project_id]);
                }
            });

        Schema::dropIfExists('beneficiary_project');
    }
};

