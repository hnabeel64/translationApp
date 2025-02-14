<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locales = Locale::factory(10)->create();
        $localeIds = $locales->pluck('id')->toArray();

        DB::disableQueryLog();
        DB::transaction(function () use ($localeIds) {
            $translations = [];
            for ($i = 0; $i < 100000; $i++) {
                $translations[] = [
                    'locale_id' => $localeIds[array_rand($localeIds)],
                    'key' => 'key_' . $i,
                    'content' => 'Sample content ' . $i,
                    'tags' => 'tag_' . rand(1, 10),
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if ($i % 5000 === 0) {
                    DB::table('translations')->insert($translations);
                    $translations = [];
                }
            }
            if (!empty($translations)) {
                DB::table('translations')->insert($translations);
            }
        });
    }
}
