<?php

namespace Database\Seeders;

use App\Models\ABTest;
use App\Models\ABTestVariant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $backgroundColorTest = ABTest::create([
            'name' => 'background_color',
            'status' => ABTest::STATUS_RUNNING,
        ]);

        $fontColorTest = ABTest::create([
            'name' => 'font_color',
            'status' => ABTest::STATUS_RUNNING,
        ]);

        ABTestVariant::insert([
            [
                'name' => 'lime',
                'targeting_ratio' => 1,
                'ab_test_id' => $backgroundColorTest->id,
            ],
            [
                'name' => 'slate',
                'targeting_ratio' => 2,
                'ab_test_id' => $backgroundColorTest->id,
            ],
            [
                'name' => 'indigo',
                'targeting_ratio' => 1,
                'ab_test_id' => $fontColorTest->id,
            ],
            [
                'name' => 'teal',
                'targeting_ratio' => 3,
                'ab_test_id' => $fontColorTest->id,
            ],
        ]);
    }
}
