<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriKosSelenggaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Alat Ganti',
                'keterangan' => 'Kos untuk pembelian alat ganti kenderaan (contoh: tayar, bateri, brake pad)',
                'aktif' => true,
            ],
            [
                'nama_kategori' => 'Upah Pembaikan',
                'keterangan' => 'Kos upah pekerja untuk pembaikan dan penyelenggaraan',
                'aktif' => true,
            ],
            [
                'nama_kategori' => 'Minyak Pelincir',
                'keterangan' => 'Kos untuk minyak enjin dan minyak pelincir lain',
                'aktif' => true,
            ],
            [
                'nama_kategori' => 'Bahan Kimia',
                'keterangan' => 'Kos untuk bahan kimia seperti cecair penyejuk, cecair pembersih',
                'aktif' => true,
            ],
            [
                'nama_kategori' => 'Penyelenggaraan Berkala',
                'keterangan' => 'Kos untuk servis berkala dan pemeriksaan rutin',
                'aktif' => true,
            ],
            [
                'nama_kategori' => 'Lain-lain',
                'keterangan' => 'Kos lain yang tidak termasuk dalam kategori di atas',
                'aktif' => true,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('kategori_kos_selenggara')->insert([
                'nama_kategori' => $category['nama_kategori'],
                'keterangan' => $category['keterangan'],
                'aktif' => $category['aktif'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
