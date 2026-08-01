<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const ADMIN_EMAIL = 'superadminstp@gmail.com';

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

        $admin = User::query()->firstOrNew(['email' => self::ADMIN_EMAIL]);
        $adminPassword = env('SEED_SUPER_ADMIN_PASSWORD');

        if (! $admin->exists && blank($adminPassword)) {
            throw new RuntimeException('Set SEED_SUPER_ADMIN_PASSWORD before creating the initial Super Admin.');
        }

        $admin->fill([
            'name' => 'Super Admin',
            'role' => 'super_admin',
        ]);
        if (filled($adminPassword)) {
            $admin->password = Hash::make($adminPassword);
        }
        $admin->save();

    }
}
