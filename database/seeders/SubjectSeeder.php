<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        Subject::query()->delete();

        $subjects = [
            ['name' => 'Bangla', 'category' => 'Core', 'class_level' => '1'],
            ['name' => 'English', 'category' => 'Core', 'class_level' => '1'],
            ['name' => 'Mathematics', 'category' => 'Core', 'class_level' => '1'],
            ['name' => 'Primary Science', 'category' => 'Core', 'class_level' => '1'],
            ['name' => 'Bangladesh and Global Studies', 'category' => 'Core', 'class_level' => '1'],
            ['name' => 'Religion and Moral Education', 'category' => 'Core', 'class_level' => '1'],

            ['name' => 'Bangla', 'category' => 'Core', 'class_level' => '2'],
            ['name' => 'English', 'category' => 'Core', 'class_level' => '2'],
            ['name' => 'Mathematics', 'category' => 'Core', 'class_level' => '2'],
            ['name' => 'Primary Science', 'category' => 'Core', 'class_level' => '2'],
            ['name' => 'Bangladesh and Global Studies', 'category' => 'Core', 'class_level' => '2'],
            ['name' => 'Religion and Moral Education', 'category' => 'Core', 'class_level' => '2'],

            ['name' => 'Bangla', 'category' => 'Core', 'class_level' => '3'],
            ['name' => 'English', 'category' => 'Core', 'class_level' => '3'],
            ['name' => 'Mathematics', 'category' => 'Core', 'class_level' => '3'],
            ['name' => 'Primary Science', 'category' => 'Core', 'class_level' => '3'],
            ['name' => 'Bangladesh and Global Studies', 'category' => 'Core', 'class_level' => '3'],
            ['name' => 'Religion and Moral Education', 'category' => 'Core', 'class_level' => '3'],

            ['name' => 'Bangla', 'category' => 'Core', 'class_level' => '4'],
            ['name' => 'English', 'category' => 'Core', 'class_level' => '4'],
            ['name' => 'Mathematics', 'category' => 'Core', 'class_level' => '4'],
            ['name' => 'Primary Science', 'category' => 'Core', 'class_level' => '4'],
            ['name' => 'Bangladesh and Global Studies', 'category' => 'Core', 'class_level' => '4'],
            ['name' => 'Religion and Moral Education', 'category' => 'Core', 'class_level' => '4'],

            ['name' => 'Bangla', 'category' => 'Core', 'class_level' => '5'],
            ['name' => 'English', 'category' => 'Core', 'class_level' => '5'],
            ['name' => 'Mathematics', 'category' => 'Core', 'class_level' => '5'],
            ['name' => 'Primary Science', 'category' => 'Core', 'class_level' => '5'],
            ['name' => 'Bangladesh and Global Studies', 'category' => 'Core', 'class_level' => '5'],
            ['name' => 'Religion and Moral Education', 'category' => 'Core', 'class_level' => '5'],

            ['name' => 'Bangla 1st Paper', 'category' => 'Core', 'class_level' => '6'],
            ['name' => 'Bangla 2nd Paper', 'category' => 'Core', 'class_level' => '6'],
            ['name' => 'English 1st Paper', 'category' => 'Core', 'class_level' => '6'],
            ['name' => 'English 2nd Paper', 'category' => 'Core', 'class_level' => '6'],
            ['name' => 'Mathematics', 'category' => 'Core', 'class_level' => '6'],
            ['name' => 'Science', 'category' => 'Core', 'class_level' => '6'],
            ['name' => 'Bangladesh and Global Studies', 'category' => 'Core', 'class_level' => '6'],
            ['name' => 'Religion and Moral Education', 'category' => 'Core', 'class_level' => '6'],
            ['name' => 'ICT', 'category' => 'Core', 'class_level' => '6'],

            ['name' => 'Bangla 1st Paper', 'category' => 'Core', 'class_level' => '7'],
            ['name' => 'Bangla 2nd Paper', 'category' => 'Core', 'class_level' => '7'],
            ['name' => 'English 1st Paper', 'category' => 'Core', 'class_level' => '7'],
            ['name' => 'English 2nd Paper', 'category' => 'Core', 'class_level' => '7'],
            ['name' => 'Mathematics', 'category' => 'Core', 'class_level' => '7'],
            ['name' => 'Science', 'category' => 'Core', 'class_level' => '7'],
            ['name' => 'Bangladesh and Global Studies', 'category' => 'Core', 'class_level' => '7'],
            ['name' => 'Religion and Moral Education', 'category' => 'Core', 'class_level' => '7'],
            ['name' => 'ICT', 'category' => 'Core', 'class_level' => '7'],

            ['name' => 'Bangla 1st Paper', 'category' => 'Core', 'class_level' => '8'],
            ['name' => 'Bangla 2nd Paper', 'category' => 'Core', 'class_level' => '8'],
            ['name' => 'English 1st Paper', 'category' => 'Core', 'class_level' => '8'],
            ['name' => 'English 2nd Paper', 'category' => 'Core', 'class_level' => '8'],
            ['name' => 'Mathematics', 'category' => 'Core', 'class_level' => '8'],
            ['name' => 'Science', 'category' => 'Core', 'class_level' => '8'],
            ['name' => 'Bangladesh and Global Studies', 'category' => 'Core', 'class_level' => '8'],
            ['name' => 'Religion and Moral Education', 'category' => 'Core', 'class_level' => '8'],
            ['name' => 'ICT', 'category' => 'Core', 'class_level' => '8'],

            ['name' => 'Bangla 1st Paper', 'category' => 'SSC', 'class_level' => '9-10'],
            ['name' => 'Bangla 2nd Paper', 'category' => 'SSC', 'class_level' => '9-10'],
            ['name' => 'English 1st Paper', 'category' => 'SSC', 'class_level' => '9-10'],
            ['name' => 'English 2nd Paper', 'category' => 'SSC', 'class_level' => '9-10'],
            ['name' => 'Mathematics', 'category' => 'SSC', 'class_level' => '9-10'],
            ['name' => 'ICT', 'category' => 'SSC', 'class_level' => '9-10'],
            ['name' => 'Bangladesh and Global Studies', 'category' => 'SSC', 'class_level' => '9-10'],
            ['name' => 'Religion and Moral Education', 'category' => 'SSC', 'class_level' => '9-10'],
            ['name' => 'Physics', 'category' => 'Science Group', 'class_level' => '9-10'],
            ['name' => 'Chemistry', 'category' => 'Science Group', 'class_level' => '9-10'],
            ['name' => 'Biology', 'category' => 'Science Group', 'class_level' => '9-10'],
            ['name' => 'Higher Mathematics', 'category' => 'Science Group', 'class_level' => '9-10'],
            ['name' => 'Accounting', 'category' => 'Business Group', 'class_level' => '9-10'],
            ['name' => 'Finance and Banking', 'category' => 'Business Group', 'class_level' => '9-10'],
            ['name' => 'Business Entrepreneurship', 'category' => 'Business Group', 'class_level' => '9-10'],
            ['name' => 'History of Bangladesh and World Civilization', 'category' => 'Humanities Group', 'class_level' => '9-10'],
            ['name' => 'Geography and Environment', 'category' => 'Humanities Group', 'class_level' => '9-10'],
            ['name' => 'Civics and Citizenship', 'category' => 'Humanities Group', 'class_level' => '9-10'],
            ['name' => 'Economics', 'category' => 'Humanities Group', 'class_level' => '9-10'],

            ['name' => 'Bangla 1st Paper', 'category' => 'HSC', 'class_level' => '11-12'],
            ['name' => 'Bangla 2nd Paper', 'category' => 'HSC', 'class_level' => '11-12'],
            ['name' => 'English 1st Paper', 'category' => 'HSC', 'class_level' => '11-12'],
            ['name' => 'English 2nd Paper', 'category' => 'HSC', 'class_level' => '11-12'],
            ['name' => 'ICT', 'category' => 'HSC', 'class_level' => '11-12'],
            ['name' => 'Physics 1st Paper', 'category' => 'Science Group', 'class_level' => '11-12'],
            ['name' => 'Physics 2nd Paper', 'category' => 'Science Group', 'class_level' => '11-12'],
            ['name' => 'Chemistry 1st Paper', 'category' => 'Science Group', 'class_level' => '11-12'],
            ['name' => 'Chemistry 2nd Paper', 'category' => 'Science Group', 'class_level' => '11-12'],
            ['name' => 'Biology 1st Paper', 'category' => 'Science Group', 'class_level' => '11-12'],
            ['name' => 'Biology 2nd Paper', 'category' => 'Science Group', 'class_level' => '11-12'],
            ['name' => 'Higher Mathematics 1st Paper', 'category' => 'Science Group', 'class_level' => '11-12'],
            ['name' => 'Higher Mathematics 2nd Paper', 'category' => 'Science Group', 'class_level' => '11-12'],
            ['name' => 'Accounting 1st Paper', 'category' => 'Business Group', 'class_level' => '11-12'],
            ['name' => 'Accounting 2nd Paper', 'category' => 'Business Group', 'class_level' => '11-12'],
            ['name' => 'Finance, Banking and Insurance 1st Paper', 'category' => 'Business Group', 'class_level' => '11-12'],
            ['name' => 'Finance, Banking and Insurance 2nd Paper', 'category' => 'Business Group', 'class_level' => '11-12'],
            ['name' => 'Business Organization and Management 1st Paper', 'category' => 'Business Group', 'class_level' => '11-12'],
            ['name' => 'Business Organization and Management 2nd Paper', 'category' => 'Business Group', 'class_level' => '11-12'],
            ['name' => 'Economics 1st Paper', 'category' => 'Humanities Group', 'class_level' => '11-12'],
            ['name' => 'Economics 2nd Paper', 'category' => 'Humanities Group', 'class_level' => '11-12'],
            ['name' => 'History 1st Paper', 'category' => 'Humanities Group', 'class_level' => '11-12'],
            ['name' => 'History 2nd Paper', 'category' => 'Humanities Group', 'class_level' => '11-12'],
            ['name' => 'Civics and Good Governance 1st Paper', 'category' => 'Humanities Group', 'class_level' => '11-12'],
            ['name' => 'Civics and Good Governance 2nd Paper', 'category' => 'Humanities Group', 'class_level' => '11-12'],
            ['name' => 'Geography 1st Paper', 'category' => 'Humanities Group', 'class_level' => '11-12'],
            ['name' => 'Geography 2nd Paper', 'category' => 'Humanities Group', 'class_level' => '11-12'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}
