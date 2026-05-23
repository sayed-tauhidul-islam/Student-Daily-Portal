<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::query()->delete();

        $canonicalAliases = [
            'government brajalal bl college' => 'government brajalal bl college',
            'government brajalal college' => 'government brajalal bl college',
            'govt brajalal college khulna' => 'government brajalal bl college',
            'govt majeed memorial city college' => 'govt majeed memorial city college',
            'govt m m city college' => 'govt majeed memorial city college',
            'govt majeed memorial city college khulna' => 'govt majeed memorial city college',
            'government joybangla college' => 'government joybangla college',
            'government joybangla college khulna' => 'government joybangla college',
            'ahsanullah college' => 'ahsanullah college',
            'ahsanullah college khulna' => 'ahsanullah college',
            'cantonment public school college' => 'cantonment public school college',
            'cantonment public school college jahanabad cantonment khulna' => 'cantonment public school college',
            'gazi medical college hospital' => 'gazi medical college hospital',
            'gazi medical college' => 'gazi medical college hospital',
            'saint joseph s high school' => 'saint joseph s high school',
            'st joseph s high school' => 'saint joseph s high school',
            'govt coronation girls high school' => 'govt coronation girls high school',
            'govt coronation secondary girls school' => 'govt coronation girls high school',
            'khulna collegiate girls school kcc women s college' => 'khulna collegiate girls school kcc women s college',
        ];

        $schools = [
            ['name' => 'Government Brajalal (B.L.) College', 'area' => 'Khulna Sadar', 'type' => 'College', 'rating' => 4.6, 'students' => 5200],
            ['name' => 'Khulna Public College', 'area' => 'Sonadanga', 'type' => 'College', 'rating' => 4.6, 'students' => 4100],
            ['name' => 'Navy Anchorage School and College Khulna', 'area' => 'Khulna Sadar', 'type' => 'School & College', 'rating' => 4.6, 'students' => 2400],
            ['name' => 'Khulna Polytechnic Institute', 'area' => 'Daulatpur', 'type' => 'College', 'rating' => 4.5, 'students' => 3900],
            ['name' => 'Bangladesh Noubahini School & College', 'area' => 'Khulna Sadar', 'type' => 'School & College', 'rating' => 4.4, 'students' => 2300],
            ['name' => 'Govt. Majeed Memorial City College, Khulna', 'area' => 'Khulna Sadar', 'type' => 'College', 'rating' => 4.4, 'students' => 4200],
            ['name' => 'Azam Khan Government Commerce College', 'area' => 'Khulna Sadar', 'type' => 'College', 'rating' => 4.4, 'students' => 3200],
            ['name' => 'Daulatpur College (Day-Night)', 'area' => 'Daulatpur', 'type' => 'College', 'rating' => 4.3, 'students' => 3000],
            ['name' => 'Government Joybangla College, Khulna', 'area' => 'Khulna Sadar', 'type' => 'College', 'rating' => 4.3, 'students' => 2800],
            ['name' => 'Lions School & College', 'area' => 'Sonadanga', 'type' => 'School & College', 'rating' => 4.3, 'students' => 1900],
            ['name' => 'Cantonment Public School & College, Jahanabad Cantonment, Khulna', 'area' => 'Khulna Sadar', 'type' => 'School & College', 'rating' => 4.3, 'students' => 2700],
            ['name' => 'Government Sundarban Adarsha College', 'area' => 'Khulna Sadar', 'type' => 'College', 'rating' => 4.2, 'students' => 2300],
            ['name' => 'Khulna Government Model School & College', 'area' => 'Khulna Sadar', 'type' => 'School & College', 'rating' => 4.2, 'students' => 2100],
            ['name' => 'Ahsanullah College,Khulna', 'area' => 'Khulna Sadar', 'type' => 'College', 'rating' => 4.1, 'students' => 1800],
            ['name' => 'Khulna Medical College', 'area' => 'Khulna Sadar', 'type' => 'College', 'rating' => 4.1, 'students' => 2600],
            ['name' => 'Gazi Medical College Hospital', 'area' => 'Khulna Sadar', 'type' => 'Medical school', 'rating' => 4.0, 'students' => 1800],
            ['name' => 'Khulna Zilla School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => 4.8, 'students' => 3200],
            ['name' => "Saint Joseph's High School", 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => 4.7, 'students' => 1600],
            ['name' => 'Government Laboratory High School Khulna', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 1300],
            ['name' => 'H.R.H Prince Aga Khan Secondary School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => 5.0, 'students' => 1200],
            ['name' => 'Deldar Ahmed Government Secondary School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 1000],
            ['name' => 'Islamabad Collegiate School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 1100],
            ['name' => 'Rotary School Khulna', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 900],
            ['name' => 'Khalishpur High School', 'area' => 'Khalishpur', 'type' => 'School', 'rating' => null, 'students' => 1000],
            ['name' => 'Khulna Collegiate School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => 4.7, 'students' => 2600],
            ['name' => 'Govt Coronation Secondary Girls School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 1400],
            ['name' => 'Pioneer Girls High School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 980],
            ['name' => 'Fatima High School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 950],
            ['name' => 'Doulatpur Muhsin High School', 'area' => 'Daulatpur', 'type' => 'School', 'rating' => null, 'students' => 980],
            ['name' => 'Govt Muhammadnagar Secondary School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 900],
            ['name' => 'Boyra Secondary School', 'area' => 'Boyra', 'type' => 'School', 'rating' => null, 'students' => 920],
            ['name' => 'Sonadanga Secondary School', 'area' => 'Sonadanga', 'type' => 'School', 'rating' => null, 'students' => 940],
            ['name' => 'Khulna Secondary School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 980],
            ['name' => 'Khulna School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => 5.0, 'students' => 1200],
            ['name' => 'Khulna College, Khulna', 'area' => 'Khulna Sadar', 'type' => 'College', 'rating' => 4.8, 'students' => 4300],
            ['name' => 'Government Brajalal College, Khulna', 'area' => 'Khulna Sadar', 'type' => 'College', 'rating' => 4.6, 'students' => 5200],
            ['name' => 'Khulna Homeopathic Medical College and Hospital', 'area' => 'Khulna Sadar', 'type' => 'College', 'rating' => 4.4, 'students' => 1400],
            ['name' => 'Khulna City Medical College', 'area' => 'Khulna Sadar', 'type' => 'College', 'rating' => 4.0, 'students' => 1700],
            ['name' => "Khulna Collegiate Girls School & KCC Women's College", 'area' => 'Khulna Sadar', 'type' => 'School & College', 'rating' => null, 'students' => 2200],
            ['name' => 'Tootpara Model Government Primary School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 700],
            ['name' => "St Joseph's Primary School", 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 650],
            ['name' => 'Nurnagar Government Primary School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 600],
            ['name' => 'Udoyon Government Primary School', 'area' => 'Khulna Sadar', 'type' => 'School', 'rating' => null, 'students' => 680],
        ];

        $toCanonicalKey = static function (string $name) use ($canonicalAliases): string {
            $normalized = strtolower(trim($name));

            // Remove common punctuation and normalize separators for tolerant matching.
            $normalized = str_replace(['.', ',', '&', '(', ')', '-', '"', "'"], ' ', $normalized);
            $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;
            $normalized = trim(preg_replace('/\s+khulna$/', '', $normalized) ?? $normalized);

            return $canonicalAliases[$normalized] ?? $normalized;
        };

        $uniqueSchools = [];
        foreach ($schools as $school) {
            $canonicalKey = $toCanonicalKey((string) ($school['name'] ?? ''));

            if ($canonicalKey === '') {
                continue;
            }

            if (! isset($uniqueSchools[$canonicalKey])) {
                $uniqueSchools[$canonicalKey] = $school;
                continue;
            }

            $current = $uniqueSchools[$canonicalKey];
            $currentRating = $current['rating'] ?? null;
            $incomingRating = $school['rating'] ?? null;

            if ($currentRating === null && $incomingRating !== null) {
                $uniqueSchools[$canonicalKey] = $school;
                continue;
            }

            if ((float) ($incomingRating ?? 0) > (float) ($currentRating ?? 0)) {
                $uniqueSchools[$canonicalKey] = $school;
                continue;
            }

            if ((int) ($school['students'] ?? 0) > (int) ($current['students'] ?? 0)) {
                $uniqueSchools[$canonicalKey] = $school;
            }
        }

        foreach ($uniqueSchools as $school) {
            School::create($school);
        }
    }
}