<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicSession;
use App\Models\AcademicTerm;
use App\Models\ClassModel;
use App\Models\ClassArm;
use App\Models\Subject;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        // Create Academic Sessions
        $session1 = AcademicSession::updateOrCreate(
            ['name' => '2023-2024'],
            [
                'start_date' => '2023-09-01',
                'end_date' => '2024-07-31',
                'is_current' => false,
                'status' => 'active',
            ]
        );

        $session2 = AcademicSession::updateOrCreate(
            ['name' => '2024-2025'],
            [
                'start_date' => '2024-09-01',
                'end_date' => '2025-07-31',
                'is_current' => true,
                'status' => 'active',
            ]
        );

        // Create Academic Terms
        AcademicTerm::updateOrCreate(
            ['academic_session_id' => $session1->id, 'name' => 'first_term'],
            [
                'start_date' => '2023-09-01',
                'end_date' => '2023-12-15',
                'is_current' => false,
                'status' => 'active',
            ]
        );

        AcademicTerm::updateOrCreate(
            ['academic_session_id' => $session1->id, 'name' => 'second_term'],
            [
                'start_date' => '2024-01-15',
                'end_date' => '2024-04-15',
                'is_current' => false,
                'status' => 'active',
            ]
        );

        AcademicTerm::updateOrCreate(
            ['academic_session_id' => $session1->id, 'name' => 'third_term'],
            [
                'start_date' => '2024-05-15',
                'end_date' => '2024-07-31',
                'is_current' => false,
                'status' => 'active',
            ]
        );

        AcademicTerm::updateOrCreate(
            ['academic_session_id' => $session2->id, 'name' => 'first_term'],
            [
                'start_date' => '2024-09-01',
                'end_date' => '2024-12-15',
                'is_current' => true,
                'status' => 'active',
            ]
        );

        AcademicTerm::updateOrCreate(
            ['academic_session_id' => $session2->id, 'name' => 'second_term'],
            [
                'start_date' => '2025-01-15',
                'end_date' => '2025-04-15',
                'is_current' => false,
                'status' => 'active',
            ]
        );

        AcademicTerm::updateOrCreate(
            ['academic_session_id' => $session2->id, 'name' => 'third_term'],
            [
                'start_date' => '2025-05-15',
                'end_date' => '2025-07-31',
                'is_current' => false,
                'status' => 'active',
            ]
        );

        // Create Classes
        $classes = [
            ['name' => 'Nursery 1', 'code' => 'N1', 'section' => 'nursery', 'display_order' => 1],
            ['name' => 'Nursery 2', 'code' => 'N2', 'section' => 'nursery', 'display_order' => 2],
            ['name' => 'Primary 1', 'code' => 'P1', 'section' => 'primary', 'display_order' => 3],
            ['name' => 'Primary 2', 'code' => 'P2', 'section' => 'primary', 'display_order' => 4],
            ['name' => 'Primary 3', 'code' => 'P3', 'section' => 'primary', 'display_order' => 5],
            ['name' => 'Primary 4', 'code' => 'P4', 'section' => 'primary', 'display_order' => 6],
            ['name' => 'Primary 5', 'code' => 'P5', 'section' => 'primary', 'display_order' => 7],
            ['name' => 'Primary 6', 'code' => 'P6', 'section' => 'primary', 'display_order' => 8],
            ['name' => 'JSS 1', 'code' => 'JSS1', 'section' => 'junior_secondary', 'display_order' => 9],
            ['name' => 'JSS 2', 'code' => 'JSS2', 'section' => 'junior_secondary', 'display_order' => 10],
            ['name' => 'JSS 3', 'code' => 'JSS3', 'section' => 'junior_secondary', 'display_order' => 11],
            ['name' => 'SSS 1', 'code' => 'SSS1', 'section' => 'senior_secondary', 'display_order' => 12],
            ['name' => 'SSS 2', 'code' => 'SSS2', 'section' => 'senior_secondary', 'display_order' => 13],
            ['name' => 'SSS 3', 'code' => 'SSS3', 'section' => 'senior_secondary', 'display_order' => 14],
        ];

        foreach ($classes as $class) {
            $class['status'] = 'active';
            ClassModel::updateOrCreate(['code' => $class['code']], $class);
        }

        // Create Class Arms
        $arms = ['A', 'B', 'C'];
        foreach (ClassModel::all() as $class) {
            foreach ($arms as $arm) {
                ClassArm::updateOrCreate(
                    ['class_id' => $class->id, 'arm_name' => $arm],
                    [
                        'capacity' => 50,
                        'status' => 'active',
                    ]
                );
            }
        }

        // Create Subjects
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH', 'category' => 'core'],
            ['name' => 'English Language', 'code' => 'ENG', 'category' => 'core'],
            ['name' => 'Basic Science', 'code' => 'BSCI', 'category' => 'core'],
            ['name' => 'Basic Technology', 'code' => 'BTECH', 'category' => 'core'],
            ['name' => 'Social Studies', 'code' => 'SST', 'category' => 'core'],
            ['name' => 'Civic Education', 'code' => 'CIVE', 'category' => 'core'],
            ['name' => 'Physical and Health Education', 'code' => 'PHE', 'category' => 'core'],
            ['name' => 'Cultural and Creative Arts', 'code' => 'CCA', 'category' => 'core'],
            ['name' => 'Home Economics', 'code' => 'HOME', 'category' => 'core'],
            ['name' => 'Computer Studies', 'code' => 'COMP', 'category' => 'core'],
            ['name' => 'French', 'code' => 'FREN', 'category' => 'elective'],
            ['name' => 'Yoruba', 'code' => 'YOR', 'category' => 'elective'],
            ['name' => 'Igbo', 'code' => 'IGB', 'category' => 'elective'],
            ['name' => 'Hausa', 'code' => 'HAU', 'category' => 'elective'],
            ['name' => 'Physics', 'code' => 'PHY', 'category' => 'science'],
            ['name' => 'Chemistry', 'code' => 'CHEM', 'category' => 'science'],
            ['name' => 'Biology', 'code' => 'BIO', 'category' => 'science'],
            ['name' => 'Further Mathematics', 'code' => 'FMATH', 'category' => 'science'],
            ['name' => 'Economics', 'code' => 'ECON', 'category' => 'commercial'],
            ['name' => 'Commerce', 'code' => 'COMM', 'category' => 'commercial'],
            ['name' => 'Accounting', 'code' => 'ACCT', 'category' => 'commercial'],
            ['name' => 'Government', 'code' => 'GOV', 'category' => 'arts'],
            ['name' => 'Literature in English', 'code' => 'LIT', 'category' => 'arts'],
            ['name' => 'Christian Religious Studies', 'code' => 'CRS', 'category' => 'arts'],
            ['name' => 'Islamic Religious Studies', 'code' => 'IRS', 'category' => 'arts'],
            ['name' => 'Data Processing', 'code' => 'DP', 'category' => 'technical'],
            ['name' => 'Business Studies', 'code' => 'BST', 'category' => 'commercial'],
        ];

        foreach ($subjects as $subject) {
            $subject['status'] = 'active';
            Subject::updateOrCreate(['code' => $subject['code']], $subject);
        }
    }
}
