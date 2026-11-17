<?php

namespace Database\Seeders;

use App\Models\IncidentCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Phase 1 seeder.
 *
 * Creates a local administrator account, a regular test user, and the incident
 * categories used by the dashboards. Credentials are taken from environment
 * variables with safe defaults; never commit real passwords.
 *
 * Run locally (requires a working PHP >= 8.4 runtime with vendor installed):
 *     php artisan db:seed --class=Phase1Seeder
 *
 * The live database is intentionally left untouched.
 */
class Phase1Seeder extends Seeder
{
    public function run(): void
    {
        // Administrator account for local testing.
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@zaxsecure.id')],
            [
                'name' => env('ADMIN_NAME', 'Administrator'),
                'email' => env('ADMIN_EMAIL', 'admin@zaxsecure.id'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'ChangeMe123!')),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Regular user account for local testing.
        User::firstOrCreate(
            ['email' => env('TEST_USER_EMAIL', 'user@zaxsecure.id')],
            [
                'name' => env('TEST_USER_NAME', 'Test User'),
                'email' => env('TEST_USER_EMAIL', 'user@zaxsecure.id'),
                'password' => Hash::make(env('TEST_USER_PASSWORD', 'TestUser123!')),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        // Incident categories used by the dashboards (idempotent).
        $categories = [
            ['name' => 'Phishing & Penipuan', 'slug' => 'phishing', 'description' => 'Email, SMS, atau situs palsu yang meniru layanan resmi.'],
            ['name' => 'Kebocoran Data', 'slug' => 'data-breach', 'description' => 'Data pribadi atau internal yang terekspos tanpa izin.'],
            ['name' => 'Akun Diretas', 'slug' => 'akun-diretas', 'description' => 'Akun diambil alih atau diakses pihak tidak berwenang.'],
            ['name' => 'Malware', 'slug' => 'malware', 'description' => 'Perangkat lunak berbahaya, ransomware, spyware.'],
            ['name' => 'Lainnya', 'slug' => 'lainnya', 'description' => 'Insiden keamanan lain di luar kategori utama.'],
        ];

        foreach ($categories as $cat) {
            IncidentCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                ['name' => $cat['name'], 'description' => $cat['description'], 'is_active' => true]
            );
        }
    }
}
