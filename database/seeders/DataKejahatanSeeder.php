<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataKejahatanSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['2020', 'Januari', 13, 32, 36, 16, 3],
            ['2020', 'Februari', 16, 24, 53, 14, 5],
            ['2020', 'Maret', 16, 35, 51, 13, 12],
            ['2020', 'April', 19, 17, 38, 9, 2],
            ['2020', 'Mei', 29, 35, 31, 11, 1],
            ['2020', 'Juni', 38, 24, 27, 6, 3],
            ['2020', 'Juli', 26, 40, 27, 14, 8],
            ['2020', 'Agustus', 24, 31, 49, 19, 3],
            ['2020', 'September', 27, 37, 159, 16, 8],
            ['2020', 'Oktober', 23, 41, 154, 20, 10],
            ['2020', 'November', 21, 35, 121, 17, 6],
            ['2020', 'Desember', 23, 41, 102, 10, 0],
            ['2021', 'Januari', 21, 29, 116, 18, 4],
            ['2021', 'Februari', 26, 32, 112, 16, 11],
            ['2021', 'Maret', 17, 19, 85, 23, 7],
            ['2021', 'April', 12, 28, 61, 12, 4],
            ['2021', 'Mei', 10, 34, 49, 17, 9],
            ['2021', 'Juni', 5, 20, 25, 10, 4],
            ['2021', 'Juli', 8, 21, 34, 13, 4],
            ['2021', 'Agustus', 7, 15, 30, 12, 6],
            ['2021', 'September', 5, 16, 31, 14, 3],
            ['2021', 'Oktober', 16, 24, 39, 14, 4],
            ['2021', 'November', 5, 20, 28, 11, 1],
            ['2021', 'Desember', 11, 20, 32, 21, 6],
            ['2022', 'Januari', 11, 31, 46, 27, 1],
            ['2022', 'Februari', 6, 20, 39, 16, 4],
            ['2022', 'Maret', 17, 22, 23, 14, 6],
            ['2022', 'April', 12, 28, 32, 17, 3],
            ['2022', 'Mei', 10, 21, 25, 12, 13],
            ['2022', 'Juni', 13, 25, 24, 15, 10],
            ['2022', 'Juli', 12, 26, 37, 22, 6],
            ['2022', 'Agustus', 9, 8, 29, 17, 14],
            ['2022', 'September', 15, 34, 50, 17, 7],
            ['2022', 'Oktober', 10, 37, 62, 28, 8],
            ['2022', 'November', 8, 18, 65, 19, 6],
            ['2022', 'Desember', 10, 18, 52, 18, 4],
            ['2023', 'Januari', 8, 31, 60, 26, 16],
            ['2023', 'Februari', 3, 18, 54, 19, 3],
            ['2023', 'Maret', 7, 34, 43, 31, 10],
            ['2023', 'April', 7, 29, 52, 17, 9],
            ['2023', 'Mei', 10, 26, 64, 18, 13],
            ['2023', 'Juni', 10, 29, 50, 17, 6],
            ['2023', 'Juli', 14, 33, 45, 25, 8],
            ['2023', 'Agustus', 11, 23, 42, 26, 13],
            ['2023', 'September', 9, 26, 27, 28, 7],
            ['2023', 'Oktober', 18, 24, 52, 43, 4],
            ['2023', 'November', 9, 25, 30, 26, 6],
            ['2023', 'Desember', 8, 26, 13, 18, 5],
            ['2024', 'Januari', 19, 30, 41, 22, 8],
            ['2024', 'Februari', 10, 21, 23, 18, 8],
            ['2024', 'Maret', 12, 15, 31, 23, 35],
            ['2024', 'April', 10, 14, 32, 18, 7],
            ['2024', 'Mei', 14, 19, 46, 17, 8],
            ['2024', 'Juni', 7, 50, 77, 18, 15],
            ['2024', 'Juli', 10, 24, 31, 24, 19],
            ['2024', 'Agustus', 13, 15, 32, 28, 6],
            ['2024', 'September', 16, 33, 52, 27, 20],
            ['2024', 'Oktober', 7, 26, 25, 23, 25],
            ['2024', 'November', 2, 19, 30, 20, 101],
            ['2024', 'Desember', 6, 18, 24, 15, 45]
        ];

        foreach ($data as $row) {
            DB::table('data_kejahatan')->insert([
                'tahun' => $row[0],
                'bulan' => $row[1],
                'curas' => $row[2],
                'curat' => $row[3],
                'curanmor' => $row[4],
                'anirat' => $row[5],
                'judi' => $row[6],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}