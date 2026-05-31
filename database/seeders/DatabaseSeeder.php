<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const ADMIN_EMAIL = 'admin@tutorlinkbd.com';

    private const ADMIN_PASSWORD = 'admin12345';

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            SubjectSeeder::class,
            SchoolSeeder::class,
            TeacherSeeder::class,
        ]);

        User::updateOrCreate([
            'email' => self::ADMIN_EMAIL,
        ], [
            'name' => 'Admin',
            'password' => Hash::make(self::ADMIN_PASSWORD),
            'role' => 'admin',
        ]);

        User::updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);
    }
}
