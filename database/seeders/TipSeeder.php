<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tips')->insert([
            [
                'title' => 'Cara menyimpan uang yang baik',
                'thumbnail' => 'nabung.jpg',
                'url' => 'https://www.fwd.co.id/id/fwdmax/passionstory-financial-literacy/first-jobber-ini-tips-menabung-yang-efektif-buat-kamu/?gclid=Cj0KCQjw5f2lBhCkARIsAHeTvljbqdUlQTDfQKlAq6adZ3YPwLPNvRupx0xh5tqp1zvK-z_E9Pb5dG8aArkuEALw_wcB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Cara berinvestasi emas',
                'thumbnail' => 'emas.jpg',
                'url' => 'https://www.fwd.co.id/id/fwdmax/passionstory-financial-literacy/investasi-emas-di-2023-apakah-menguntungkan/?gclid=Cj0KCQjw5f2lBhCkARIsAHeTvlhblbvl1-hgO9scHIP39vTdighQwnpGPx6qsvuP3P4ooQJbx62A7l8aAoZCEALw_wcB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Cara menyimpan saham',
                'thumbnail' => 'saham.jpg',
                'url' => 'https://www.idx.co.id/id/produk/saham',
                'created_at' => now(),
                'updated_at' => now(),
            ],   
        ]);
    }
}
