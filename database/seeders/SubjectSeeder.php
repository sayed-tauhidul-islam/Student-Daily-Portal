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
            ['name' => 'Bangla', 'category' => 'Language'],
            ['name' => 'English', 'category' => 'Language'],
            ['name' => 'English Grammar', 'category' => 'Language'],
            ['name' => 'Spoken English', 'category' => 'Language'],
            ['name' => 'Mathematics', 'category' => 'Science'],
            ['name' => 'Higher Mathematics', 'category' => 'Science'],
            ['name' => 'Physics', 'category' => 'Science'],
            ['name' => 'Chemistry', 'category' => 'Science'],
            ['name' => 'Biology', 'category' => 'Science'],
            ['name' => 'ICT', 'category' => 'Technology'],
            ['name' => 'Computer Science', 'category' => 'Technology'],
            ['name' => 'Web Development', 'category' => 'Technology'],
            ['name' => 'Programming Fundamentals', 'category' => 'Technology'],
            ['name' => 'Accounting', 'category' => 'Commerce'],
            ['name' => 'Finance', 'category' => 'Commerce'],
            ['name' => 'Business Studies', 'category' => 'Commerce'],
            ['name' => 'Economics', 'category' => 'Commerce'],
            ['name' => 'Civics', 'category' => 'Humanities'],
            ['name' => 'History', 'category' => 'Humanities'],
            ['name' => 'Geography', 'category' => 'Humanities'],
            ['name' => 'Islamic Studies', 'category' => 'Humanities'],
            ['name' => 'Agriculture', 'category' => 'Science'],
            ['name' => 'Statistics', 'category' => 'Science'],
            ['name' => 'Psychology', 'category' => 'Humanities'],
            ['name' => 'Sociology', 'category' => 'Humanities'],
            ['name' => 'Drawing', 'category' => 'Arts'],
            ['name' => 'Music', 'category' => 'Arts'],
            ['name' => 'Arabic', 'category' => 'Language'],
            ['name' => 'Hindi', 'category' => 'Language'],
            ['name' => 'Logic', 'category' => 'Humanities'],
            ['name' => 'Ethics', 'category' => 'Humanities'],
            ['name' => 'Bangladesh Studies', 'category' => 'Humanities'],
            ['name' => 'Public Speaking', 'category' => 'Language'],
            ['name' => 'General Science', 'category' => 'Science'],
            ['name' => 'Physics Lab', 'category' => 'Science'],
            ['name' => 'Chemistry Lab', 'category' => 'Science'],
            ['name' => 'Biology Lab', 'category' => 'Science'],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }
    }
}