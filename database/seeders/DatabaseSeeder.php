<?php

namespace Database\Seeders;

use App\Models\DailyMetric;
use App\Models\Grapari;
use App\Models\SocialContent;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF;');
        DailyMetric::truncate();
        SocialContent::truncate();
        Grapari::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        $graparisData = [
            [
                'rank' => 1,
                'name' => 'GraPARI Manado',
                'short_name' => 'Manado',
                'address' => 'Jl. Piere Tendean (Boulevard), Sario Tumpaan, Kota Manado',
            ],
            [
                'rank' => 2,
                'name' => 'GraPARI Ternate',
                'short_name' => 'Ternate',
                'address' => 'Jl. Pahlawan Revolusi, Muhajirin, Kota Ternate',
            ],
            [
                'rank' => 3,
                'name' => 'GraPARI Tomohon',
                'short_name' => 'Tomohon',
                'address' => 'Jl. Raya Tomohon, Paslaten Satu, Kota Tomohon',
            ],
            [
                'rank' => 4,
                'name' => 'GraPARI Bitung',
                'short_name' => 'Bitung',
                'address' => 'Jl. Sam Ratulangi, Bitung Tengah, Kota Bitung',
            ],
            [
                'rank' => 5,
                'name' => 'GraPARI Airmadidi',
                'short_name' => 'Airmadidi',
                'address' => 'Jl. Arnold Mononutu, Airmadidi Atas, Minahasa Utara',
            ],
            [
                'rank' => 6,
                'name' => 'GraPARI Tahuna',
                'short_name' => 'Tahuna',
                'address' => 'Jl. Tatehe, Bungalawang, Tahuna, Kepulauan Sangihe',
            ],
            [
                'rank' => 7,
                'name' => 'GraPARI Tobelo',
                'short_name' => 'Tobelo',
                'address' => 'Jl. Kemakmuran, Gosoma, Tobelo, Halmahera Utara',
            ],
            [
                'rank' => 8,
                'name' => 'GraPARI Obi',
                'short_name' => 'Obi',
                'address' => 'Jl. Raya Laiwui, Pulau Obi, Halmahera Selatan',
            ],
            [
                'rank' => 9,
                'name' => 'GraPARI Weda',
                'short_name' => 'Weda',
                'address' => 'Jl. Raya Were, Weda, Halmahera Tengah',
            ],
            [
                'rank' => 10,
                'name' => 'GraPARI Labuha',
                'short_name' => 'Labuha',
                'address' => 'Jl. Karet Putih, Labuha, Bacan, Halmahera Selatan',
            ],
        ];

        foreach ($graparisData as $g) {
            Grapari::create(array_merge($g, [
                'total_contents' => 0,
                'likes' => 0,
                'comments' => 0,
                'direct_chats' => 0,
                'chart_likes' => 0,
                'chart_comments' => 0,
                'chart_direct_chats' => 0,
                'engagement_rate' => 0.0,
            ]));
        }

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $startOfMonth = Carbon::now()->startOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startOfMonth->copy()->day($day);
            $label = $date->day.' '.$months[$date->month];

            DailyMetric::create([
                'metric_date' => $date->toDateString(),
                'label' => $label,
                'likes' => 0,
                'comments' => 0,
                'direct_chats' => 0,
            ]);
        }
    }
}
