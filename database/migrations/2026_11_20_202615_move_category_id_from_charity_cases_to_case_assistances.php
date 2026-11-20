<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The category describes the need, so it moves from the beneficiary
 * (charity_cases) to each assistance. Every existing assistance takes its
 * beneficiary's category, so nothing is lost.
 *
 * It also turns descriptions saved as one plain string into the {en, ar}
 * shape the model now reads, keeping the text in both languages.
 */
return new class extends Migration
{
    public function up(): void
    {
        // MySQL cannot change a column that already has a foreign key, so the key is added last.
        Schema::table('case_assistances', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('case_id');
        });

        DB::table('case_assistances')->update([
            'category_id' => DB::raw('(SELECT category_id FROM charity_cases WHERE charity_cases.id = case_assistances.case_id)'),
        ]);

        Schema::table('case_assistances', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable(false)->change();
        });

        Schema::table('case_assistances', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('case_categories')->restrictOnDelete();
        });

        Schema::table('charity_cases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });

        $this->translateDescriptions();
    }

    public function down(): void
    {
        Schema::table('charity_cases', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('phone')->constrained('case_categories')->restrictOnDelete();
        });

        // A beneficiary takes the category of their first assistance; one without assistances keeps NULL.
        DB::table('charity_cases')->update([
            'category_id' => DB::raw('(SELECT category_id FROM case_assistances WHERE case_assistances.case_id = charity_cases.id ORDER BY case_assistances.id LIMIT 1)'),
        ]);

        Schema::table('case_assistances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }

    /** A description stored as one string becomes {en, ar} with that text in both. */
    private function translateDescriptions(): void
    {
        DB::table('case_assistances')
            ->whereNotNull('description')
            ->orderBy('id')
            ->get(['id', 'description'])
            ->each(function (object $row): void {
                $decoded = json_decode($row->description, true);

                if (is_array($decoded)) {
                    return;
                }

                $text = is_string($decoded) ? $decoded : $row->description;

                DB::table('case_assistances')->where('id', $row->id)->update([
                    'description' => json_encode(['en' => $text, 'ar' => $text], JSON_UNESCAPED_UNICODE),
                ]);
            });
    }
};
