<?php

namespace Database\Seeders;

use App\Models\IncidentCategory;
use Illuminate\Database\Seeder;

class IncidentCategorySeeder extends Seeder
{
    /**
     * Seed the initial incident categories.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Phishing & Penipuan',
                'slug' => 'phishing-penipuan',
                'description' => 'Upaya penipuan online, pencurian kredensial, dan rekayasa sosial berbasis pesan.',
                'is_active' => true,
            ],
            [
                'name' => 'Kebocoran Data',
                'slug' => 'kebocoran-data',
                'description' => 'Eksposur, peretasan basis data, atau penyebaran data rahasia tanpa izin.',
                'is_active' => true,
            ],
            [
                'name' => 'Akun Diretas',
                'slug' => 'akun-diretas',
                'description' => 'Pengambilalihan akun pengguna, credential stuffing, atau kompromi kata sandi.',
                'is_active' => true,
            ],
            [
                'name' => 'Malware',
                'slug' => 'malware',
                'description' => 'Infeksi perangkat lunak berbahaya, ransomware, trojan, spyware, dan virus.',
                'is_active' => true,
            ],
            [
                'name' => 'Akses Tidak Sah',
                'slug' => 'akses-tidak-sah',
                'description' => 'Penyusupan jaringan, eskalasi hak akses, dan pelanggaran kontrol otorisasi.',
                'is_active' => true,
            ],
            [
                'name' => 'Social Engineering',
                'slug' => 'social-engineering',
                'description' => 'Manipulasi psikologis terhadap personil untuk membocorkan informasi sensitif.',
                'is_active' => true,
            ],
            [
                'name' => 'Lainnya',
                'slug' => 'lainnya',
                'description' => 'Insiden keamanan siber lainnya yang tidak tercakup dalam kategori utama.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            IncidentCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
