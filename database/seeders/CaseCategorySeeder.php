<?php

namespace Database\Seeders;

use App\Models\CaseCategory;
use Illuminate\Database\Seeder;

/** The base case categories. Safe to run again: an existing English name is skipped. */
class CaseCategorySeeder extends Seeder
{
    /** @var list<array{en: string, ar: string}> */
    private const CATEGORIES = [
        ['en' => 'Food', 'ar' => 'غذاء'],
        ['en' => 'Housing', 'ar' => 'سكن'],
        ['en' => 'Medical', 'ar' => 'علاج'],
        ['en' => 'Education', 'ar' => 'تعليم'],
        ['en' => 'Other', 'ar' => 'أخرى'],
    ];

    public function run(): void
    {
        $existing = CaseCategory::query()->get()->map(fn (CaseCategory $category) => $category->nameLang('en'))->all();

        foreach (self::CATEGORIES as $index => $name) {
            if (! in_array($name['en'], $existing, true)) {
                CaseCategory::create(['name' => $name, 'order_id' => $index + 1, 'active' => 1]);
            }
        }
    }
}
