<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradingScale;

class GradingScaleSeeder extends Seeder
{
    public function run(): void
    {
        $scales = [
            ['grade' => 'A', 'min_score' => 70, 'max_score' => 100, 'remark' => 'Excellent', 'display_order' => 1],
            ['grade' => 'B', 'min_score' => 60, 'max_score' => 69, 'remark' => 'Very Good', 'display_order' => 2],
            ['grade' => 'C', 'min_score' => 50, 'max_score' => 59, 'remark' => 'Good', 'display_order' => 3],
            ['grade' => 'D', 'min_score' => 45, 'max_score' => 49, 'remark' => 'Fair', 'display_order' => 4],
            ['grade' => 'E', 'min_score' => 40, 'max_score' => 44, 'remark' => 'Pass', 'display_order' => 5],
            ['grade' => 'F', 'min_score' => 0, 'max_score' => 39, 'remark' => 'Fail', 'display_order' => 6],
        ];

        foreach ($scales as $scale) {
            GradingScale::updateOrCreate(
                ['grade' => $scale['grade']],
                [
                    'name' => 'Standard Grading',
                    'min_score' => $scale['min_score'],
                    'max_score' => $scale['max_score'],
                    'remark' => $scale['remark'],
                    'is_active' => true,
                    'display_order' => $scale['display_order'],
                ]
            );
        }
    }
}
