<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        Teacher::query()->delete();

        $teacherDirectory = [
            ['institution' => 'Government Brajalal College, Khulna', 'area' => 'Khulna Sadar', 'rating' => 4.6, 'teachers' => [
                ['Prof. Sharif Atiquzzaman', 'Principal'],
                ['Prof. Dr. Khandakar Hamidul Islam', 'Islamic History'],
                ['Dr. Sheikh Mamunur Rahman', 'Associate Professor'],
                ['Md. Abdur Rahim', 'Lecturer'],
                ['Kaniz Akter', 'Lecturer'],
            ]],
            ['institution' => 'Khulna Public College', 'area' => 'Sonadanga', 'rating' => 4.6, 'teachers' => [
                ['Lt. Col. Munir Abbas', 'Principal'],
                ['Md. Harunur Rashid', 'Associate Professor'],
                ['Md. Latifur Rahman Khan', 'Physics'],
                ['Phanibhushan Sikder', 'Senior Faculty'],
                ['Md. Shahedul Haque', 'English'],
            ]],
            ['institution' => 'Navy Anchorage School and College Khulna', 'area' => 'Khulna Sadar', 'rating' => 4.6, 'teachers' => [
                ['Commander M. M. Rahman', 'Principal'],
                ['Shrabani Gope', 'Assistant Teacher'],
                ['Lubna Chowdhury', 'Assistant Teacher'],
                ['Kamrunnahar', 'Faculty'],
                ['Rubia Laila', 'English Faculty'],
            ]],
            ['institution' => 'Govt. Majeed Memorial City College, Khulna', 'area' => 'Khulna Sadar', 'rating' => 4.4, 'teachers' => [
                ['Md. Imran Ali', 'Lecturer - Accounting'],
                ['Shiful Alam', 'Lecturer - Marketing'],
                ['Mohammad Badsha Mia', 'Lecturer - English'],
                ['Rokeya Begum', 'Bangla Dept'],
                ['Kalyan Kumar', 'Mathematics'],
            ]],
            ['institution' => 'Azam Khan Government Commerce College', 'area' => 'Khulna Sadar', 'rating' => 4.4, 'teachers' => [
                ['Mahnta Kumar', 'Assistant Professor - Accounting'],
                ['M. A. Hasan', 'Business English'],
                ['Tania Sultana', 'Finance Faculty'],
                ['Md. Ilias', 'Management Studies'],
                ['Shamim Ahmed', 'Marketing Lead'],
            ]],
            ['institution' => 'Daulatpur College (Day-Night)', 'area' => 'Daulatpur', 'rating' => 4.3, 'teachers' => [
                ['Sabina Rowshan', 'Assistant Professor'],
                ['Mst. Alsania', 'Lecturer - Accounting'],
                ['Anisur Rahman', 'Bangla Dept'],
                ['Farhana Akter', 'English Lecturer'],
                ['Md. Jashim Uddin', 'Social Science'],
            ]],
            ['institution' => 'Khulna Government College, Khulna', 'area' => 'Khulna Sadar', 'rating' => 4.3, 'teachers' => [
                ['Dr. Asif Iqbal', 'Science Faculty'],
                ['Swapna Sarker', 'Humanities Lead'],
                ['M. R. Rahman', 'Bangla Lecturer'],
                ['Farzana Yasmin', 'ICT Instructor'],
                ['Md. Rafiqul Islam', 'History'],
            ]],
            ['institution' => 'Lions School & College', 'area' => 'Sonadanga', 'rating' => 4.3, 'teachers' => [
                ['Md. Nasir Uddin', 'Principal'],
                ['Sabina Yasmin', 'Senior Teacher'],
                ['M. A. Khaleque', 'Mathematics'],
                ['Nasrin Akter', 'English Lecturer'],
                ['Abdur Razzak', 'Science Instructor'],
            ]],
            ['institution' => 'Cantonment Public School & College, Jahanabad Cantonment, Khulna', 'area' => 'Khulna Sadar', 'rating' => 4.3, 'teachers' => [
                ['Lt. Col. Md. Mizanur Rahman', 'Principal'],
                ['Major Shafiul Alam', 'Vice Principal'],
                ['Kazi Mohammad Ali', 'Senior Lecturer'],
                ['Tahmina Akter', 'Chemistry Faculty'],
                ['Md. Mahmudul Haque', 'Computer Science'],
            ]],
            ['institution' => 'Government Sundarban Adarsha College', 'area' => 'Khulna Sadar', 'rating' => 4.2, 'teachers' => [
                ['Prof. Md. Hashim Ali', 'Principal'],
                ['Sultana Razia', 'Associate Professor'],
                ['Md. Kamruzzaman', 'Economics'],
                ['Ferdousi Begum', 'Political Science'],
                ['S. M. Abu Bakar', 'Botany Faculty'],
            ]],
            ['institution' => 'Khulna Government Model School & College', 'area' => 'Khulna Sadar', 'rating' => 4.2, 'teachers' => [
                ['Md. Abdul Karim', 'Principal'],
                ['Md. Bazlur Rashid', 'History'],
                ['Md. Salahuddin', 'Logic'],
                ['A. T. M. Asaduzzaman', 'Mathematics'],
                ['Sharmin Sultana', 'English'],
            ]],
            ['institution' => 'Ahsanullah College,Khulna', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Md. Ahsanullah', 'Founder Principal'],
                ['Rowshan Ara', 'Vice Principal'],
                ['Md. Ziaul Haque', 'Accounting'],
                ['Salma Khatun', 'Social Welfare'],
                ['Arifur Rahman', 'English Faculty'],
            ]],
            ['institution' => 'Khulna College, Khulna', 'area' => 'Khulna Sadar', 'rating' => 4.8, 'teachers' => [
                ['Md. Mizanur Rahman', 'Principal'],
                ['Nazma Begum', 'Vice Principal'],
                ['Shahanaj Parvin', 'Bangla Lecturer'],
                ['Md. Tariqul Islam', 'Economics'],
                ['Asma Ul Hosna', 'Logic Faculty'],
            ]],
            ['institution' => 'Khulna Polytechnic Institute', 'area' => 'Daulatpur', 'rating' => 4.5, 'teachers' => [
                ['Mahabub Alam', 'Chief Instructor'],
                ['Mithun Sarkar', 'Physics'],
                ['Shariful Islam', 'Bangla'],
                ['Md. Nazmul Hossain', 'Technical Instructor'],
                ['Engr. Md. Saiful Islam', 'Electronics'],
            ]],
            ['institution' => 'Bangladesh Noubahini School & College', 'area' => 'Khulna Sadar', 'rating' => 4.4, 'teachers' => [
                ['Commander S. M. Khaled', 'Principal'],
                ['Md. Nurul Amin', 'Vice Principal'],
                ['Asif Ahmed', 'Senior Mathematics'],
                ['Nishir Sultana', 'English Dept'],
                ['Md. Moniruzzaman', 'Physics Lead'],
            ]],
            ['institution' => 'Khulna Homeopathic Medical College and Hospital', 'area' => 'Khulna Sadar', 'rating' => 4.4, 'teachers' => [
                ['Dr. Md. Abdul Jalil', 'Principal'],
                ['Dr. S. M. Hasan', 'Organon of Medicine'],
                ['Dr. Fatema Begum', 'Anatomy'],
                ['Dr. Arifur Rahman', 'Materia Medica'],
                ['Dr. Sabina Yasmin', 'Pathology'],
            ]],
            ['institution' => 'Khulna Medical College', 'area' => 'Khulna Sadar', 'rating' => 4.1, 'teachers' => [
                ['Prof. Dr. Din Ul Islam', 'Principal'],
                ['Dr. SM Tushar Alam', 'Anatomy'],
                ['Prof. Dr. Mahbubul Alam', 'Medicine'],
                ['Dr. Shamsun Nahar', 'Gynecology'],
                ['Dr. Md. Kamrul Islam', 'Surgery'],
            ]],
            ['institution' => 'Gazi Medical College Hospital', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Dr. Tasnuva Gazi', 'Lecturer'],
                ['Prof. Dr. Bangamata Sheikh Fojilatunnesa', 'Senior Faculty'],
                ['Dr. Md. Moklesur Rahman', 'Anatomy'],
                ['Dr. Farhana Yasmin', 'Physiology'],
                ['Dr. S. K. Biswas', 'Biochemistry'],
            ]],
            ['institution' => 'Khulna City Medical College', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Prof. Dr. S. M. K. Zaman', 'Principal'],
                ['Dr. Monira Parveen', 'Microbiology'],
                ['Dr. Md. Yunus Ali', 'Pharmacology'],
                ['Dr. Sayed Al Mamun', 'Forensic Medicine'],
                ['Dr. Rebecca Sultana', 'Community Medicine'],
            ]],
            ['institution' => 'Khulna Zilla School', 'area' => 'Khulna Sadar', 'rating' => 4.8, 'teachers' => [
                ['Md. Farukul Islam', 'Headmaster'],
                ['Chinmoy Kumar Das', 'Senior Teacher'],
                ['Md. Abu Bakar', 'English'],
                ['Sheikh Moniruzzaman', 'Mathematics'],
                ['A. K. M. Mazharul Islam', 'Science'],
            ]],
            ['institution' => "Saint Joseph's High School", 'area' => 'Khulna Sadar', 'rating' => 4.7, 'teachers' => [
                ['Brother Leo Pereira', 'Headmaster'],
                ['Alphonse Mondal', 'Assistant Headmaster'],
                ['Sylvester Costa', 'Senior English Teacher'],
                ['Subrata Roy', 'Mathematics Faculty'],
                ['Protonu Biswas', 'Science Specialist'],
            ]],
            ['institution' => 'Government Laboratory High School Khulna', 'area' => 'Khulna Sadar', 'rating' => 4.2, 'teachers' => [
                ['Md. Abu Said', 'Headmaster'],
                ['Nasrin Sultana', 'Assistant Headmistress'],
                ['Md. Tariqul Islam', 'General Science'],
                ['Rahima Khatun', 'Bangla'],
                ['Md. Anwar Hossain', 'Mathematics'],
            ]],
            ['institution' => 'H.R.H Prince Aga Khan Secondary School', 'area' => 'Khulna Sadar', 'rating' => 5.0, 'teachers' => [
                ['Md. Shamsul Alam', 'Headmaster'],
                ['Farzana Chowdhury', 'English Language'],
                ['Md. Mizanur Rahman', 'Mathematics'],
                ['Shahnaz Begum', 'Social Science'],
                ['Kamrul Hasan', 'Physical Science'],
            ]],
            ['institution' => 'Deldar Ahmed Government Secondary School', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Md. Deldar Ahmed', 'Founder Principal'],
                ['Jesmin Ara', 'Headmistress'],
                ['Md. Shafiqul Islam', 'Mathematics'],
                ['Tania Rahman', 'English Faculty'],
                ['Asaduzzaman Khan', 'History'],
            ]],
            ['institution' => 'Islamabad Collegiate School', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Md. Nurul Islam', 'Headmaster'],
                ['Ferdousi Akter', 'Assistant Teacher'],
                ['Md. Ruhul Amin', 'Science Faculty'],
                ['Sharmin Sultana', 'English Specialist'],
                ['Abul Kalam Azad', 'Mathematics'],
            ]],
            ['institution' => 'Rotary School Khulna', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Md. Aslam Hossain', 'Headmaster'],
                ['Rasheda Begum', 'Senior Assistant Teacher'],
                ['Md. Golam Mustafa', 'Mathematics'],
                ['Zeenat Ara', 'Bangla Dept'],
                ['S. M. Iqbal', 'General Science'],
            ]],
            ['institution' => 'Khalishpur High School', 'area' => 'Khalishpur', 'rating' => 4.0, 'teachers' => [
                ['Md. Shahjahan Ali', 'Headmaster'],
                ['Anwara Begum', 'Assistant Headmistress'],
                ['Md. Motiur Rahman', 'English'],
                ['Sultana Razia', 'Social Science'],
                ['Md. Ali Hossain', 'Mathematics'],
            ]],
            ['institution' => 'Khulna Collegiate School', 'area' => 'Khulna Sadar', 'rating' => 4.7, 'teachers' => [
                ['Md. Tariqul Islam', 'Headmaster'],
                ['Hosne Ara Begum', 'Bangla Dept'],
                ['Md. Ziaur Rahman', 'Mathematics'],
                ['Farhana Chowdhury', 'English Faculty'],
                ['Kamrul Islam', 'Physical Science'],
            ]],
            ['institution' => 'Govt Coronation Secondary Girls School', 'area' => 'Khulna Sadar', 'rating' => 4.2, 'teachers' => [
                ['Monira Begum', 'Headmistress'],
                ['Nasrin Akter', 'Assistant Teacher'],
                ['Rehana Parvin', 'Senior English Teacher'],
                ['Md. Habibur Rahman', 'Mathematics'],
                ['Salma Khatun', 'Geography'],
            ]],
            ['institution' => 'Pioneer Girls High School', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Hosne Ara Rahman', 'Headmistress'],
                ['Tanzila Akter', 'Senior English Teacher'],
                ['Md. Abdur Rashid', 'Mathematics'],
                ['Nasima Khatun', 'Social Science'],
                ['Rowshan Ara', 'General Science'],
            ]],
            ['institution' => 'Fatima High School', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Sister Mary Rita', 'Headmistress'],
                ['Theresa Gomes', 'Senior Faculty'],
                ['Md. Abdul Wadud', 'Mathematics'],
                ['Farida Yasmin', 'English Faculty'],
                ['Subrata Kumar Das', 'Science'],
            ]],
            ['institution' => 'Doulatpur Muhsin High School', 'area' => 'Daulatpur', 'rating' => 4.0, 'teachers' => [
                ['Md. Mohsin Ali', 'Headmaster'],
                ['S. M. Abu Bakar', 'Mathematics'],
                ['Khatune Jannat', 'Bangla Faculty'],
                ['Md. Nazrul Islam', 'English Lead'],
                ['Suraiya Begum', 'General Science'],
            ]],
            ['institution' => 'Govt Muhammadnagar Secondary School', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Md. Habibur Rahman', 'Headmaster'],
                ['Amena Begum', 'Assistant Teacher'],
                ['Md. Tariqul Islam', 'Science Faculty'],
                ['Farzana Akter', 'English'],
                ['Md. Yunus Ali', 'Mathematics'],
            ]],
            ['institution' => 'Boyra Secondary School', 'area' => 'Boyra', 'rating' => 4.0, 'teachers' => [
                ['Md. Shafiqul Islam', 'Headmaster'],
                ['Rowshan Ara Begum', 'Bangla Department'],
                ['Md. Azhar Ali', 'Mathematics'],
                ['Tania Sultana', 'English Instructor'],
                ['Md. Kamruzzaman', 'Social Science'],
            ]],
            ['institution' => 'Sonadanga Secondary School', 'area' => 'Sonadanga', 'rating' => 4.0, 'teachers' => [
                ['Md. Rafiqul Islam', 'Headmaster'],
                ['Shahanaj Parvin', 'Assistant Headmistress'],
                ['Md. Ilias', 'Senior Math Teacher'],
                ['Salma Begum', 'English Dept'],
                ['Md. Abu Bakar', 'General Science'],
            ]],
            ['institution' => 'Khulna Secondary School', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Md. Nazrul Islam', 'Headmaster'],
                ['Fatema Khatun', 'Senior Teacher'],
                ['Md. Abdul Karim', 'Mathematics'],
                ['Farhana Akter', 'English Language'],
                ['S. M. Aminur Rahman', 'Science Faculty'],
            ]],
            ['institution' => 'Khulna School', 'area' => 'Khulna Sadar', 'rating' => 5.0, 'teachers' => [
                ['Md. Mizanur Rahman', 'Headmaster'],
                ['Sultana Razia', 'Assistant Headmistress'],
                ['Md. Asif Iqbal', 'Mathematics'],
                ['Tania Rahman', 'English Specialist'],
                ['Md. Tariqul Islam', 'Social Science'],
            ]],
            ['institution' => "Khulna Collegiate Girls School & KCC Women's College", 'area' => 'Khulna Sadar', 'rating' => 4.1, 'teachers' => [
                ['Prof. Md. Abdul Jalil', 'Principal'],
                ['Dr. Shahanaj Begum', 'Vice Principal'],
                ['Farhana Akter', 'Lecturer - English'],
                ['Md. Ruhul Amin', 'Lecturer - Chemistry'],
                ['Sultana Razia', 'Lecturer - Bangla'],
            ]],
            ['institution' => 'Tootpara Model Government Primary School', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Nasrin Akter', 'Headmistress'],
                ['Md. Habibur Rahman', 'Assistant Teacher'],
                ['Salma Khatun', 'Mathematics Specialist'],
                ['Rowshan Ara', 'Bangla Teacher'],
                ['Md. Mizanur Rahman', 'Social Studies'],
            ]],
            ['institution' => "St Joseph's Primary School", 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Sister Mary Teresa', 'Headmistress'],
                ['Alphonse Mondal', 'Senior Coordinator'],
                ['Theresa Gomes', 'Primary Educator'],
                ['Md. Abdul Wadud', 'Basic Math'],
                ['Farida Yasmin', 'English Language'],
            ]],
            ['institution' => 'Nurnagar Government Primary School', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Sultana Razia', 'Headmistress'],
                ['Md. Tariqul Islam', 'Assistant Teacher'],
                ['Farzana Akter', 'Language Teacher'],
                ['Md. Yunus Ali', 'Arithmetic'],
                ['Amena Begum', 'General Science'],
            ]],
            ['institution' => 'Udoyon Government Primary School', 'area' => 'Khulna Sadar', 'rating' => 4.0, 'teachers' => [
                ['Rowshan Ara Begum', 'Headmistress'],
                ['Md. Azhar Ali', 'Senior Assistant Teacher'],
                ['Tania Sultana', 'English Basics'],
                ['Md. Kamruzzaman', 'Social Environment'],
                ['Fatema Khatun', 'Bangla Language'],
            ]],
        ];

        $subjectFromRole = static function (string $role): string {
            $roleLower = strtolower($role);
            $map = [
                'english' => 'English',
                'bangla' => 'Bangla',
                'math' => 'Mathematics',
                'physics' => 'Physics',
                'chemistry' => 'Chemistry',
                'biology' => 'Biology',
                'science' => 'General Science',
                'history' => 'History',
                'economics' => 'Economics',
                'geography' => 'Geography',
                'ict' => 'ICT',
                'computer' => 'Computer Science',
                'accounting' => 'Accounting',
                'finance' => 'Finance',
                'marketing' => 'Business Studies',
                'management' => 'Business Studies',
                'logic' => 'Logic',
                'political' => 'Civics',
                'botany' => 'Biology',
                'pharmacology' => 'Biology',
                'anatomy' => 'Biology',
                'medicine' => 'Biology',
                'surgery' => 'Biology',
                'microbiology' => 'Biology',
                'forensic' => 'Biology',
                'pathology' => 'Biology',
                'gynecology' => 'Biology',
                'electronic' => 'ICT',
                'technical' => 'ICT',
                'social' => 'Social Science',
            ];

            foreach ($map as $keyword => $subject) {
                if (str_contains($roleLower, $keyword)) {
                    return $subject;
                }
            }

            return 'General Studies';
        };

        $qualificationFromRole = static function (string $name, string $role): string {
            $haystack = strtolower($name . ' ' . $role);

            if (str_contains($haystack, 'prof') || str_contains($haystack, 'principal')) {
                return 'Masters / Senior Academic';
            }

            if (str_contains($haystack, 'dr')) {
                return 'Doctorate';
            }

            if (str_contains($haystack, 'lecturer') || str_contains($haystack, 'teacher') || str_contains($haystack, 'faculty')) {
                return 'Masters';
            }

            return 'Graduate';
        };

        $experienceFromRole = static function (string $role): string {
            $roleLower = strtolower($role);

            if (str_contains($roleLower, 'principal') || str_contains($roleLower, 'headmaster') || str_contains($roleLower, 'headmistress')) {
                return '12 years';
            }

            if (str_contains($roleLower, 'senior') || str_contains($roleLower, 'associate')) {
                return '9 years';
            }

            if (str_contains($roleLower, 'assistant')) {
                return '5 years';
            }

            return '7 years';
        };

        foreach ($teacherDirectory as $institutionEntry) {
            foreach ($institutionEntry['teachers'] as [$name, $role]) {
                $primarySubject = $subjectFromRole($role);

                Teacher::create([
                    'name' => trim($name),
                    'qualification' => $qualificationFromRole($name, $role),
                    'experience' => $experienceFromRole($role),
                    'subject' => $primarySubject,
                    'subjects' => [$primarySubject],
                    'salary' => str_contains(strtolower($role), 'principal') || str_contains(strtolower($role), 'head') ? 10000 : 7000,
                    'area' => $institutionEntry['area'],
                    'bio' => trim($role) . ' at ' . $institutionEntry['institution'],
                    'rating' => $institutionEntry['rating'],
                    'image' => null,
                    'availability' => 'Evening',
                    'institution' => $institutionEntry['institution'],
                    'verification_status' => 'verified',
                ]);
            }
        }
    }
}