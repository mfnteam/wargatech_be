<?php

namespace Database\Seeders;

use App\Models\LrtTrackway;
use App\Models\Trackway;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $train = [
            [
            'track_name' => 'bogor-jakarta',
            'station_passed' => '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24',
            'travel_time' => '0,9,6,6,6,4,2,4,3,1,2,7,3,1,2,2,5,2,2,7,1,1,6,3'
            ],
            [
            'track_name' => 'nambo-jakarta',
            'station_passed' => '26,25,27,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24',
            'travel_time' => '0,11,3,10,6,4,2,4,3,1,2,7,3,1,2,2,5,2,2,7,1,1,6,3'
            ],
            [
            'track_name' => 'cikarang-kampungbandan via pse',
            'station_passed' => '28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46',
            'travel_time' => '0,4,2,7,4,5,2,6,2,1,2,10,2,2,1,8,4,3,6'
            ],
            [
            'track_name' => 'bekasi-kampungbandan via pse',
            'station_passed' => '33,34,35,36,37,38,39,40,41,42,43,44,45,46',
            'travel_time' => '0,2,6,2,1,2,10,2,2,1,8,4,3,6'
            ],
            [
            'track_name' => 'cikarang-kampungbandan via mri',
            'station_passed' => '28,29,30,31,32,33,34,35,36,37,38,39,47,17,48,49,50,51,52,53,46',
            'travel_time' => '0,4,2,2,2,4,5,2,6,2,1,2,8,2,5,4,0,1,7,6,5,10'
            ],
            [
            'track_name' => 'bekasi-kampungbandan via mri',
            'station_passed' => '33,34,35,36,37,38,39,47,17,48,49,50,51,52,53,46',
            'travel_time' => '0,5,2,6,2,1,2,8,2,5,4,0,1,7,6,5,10'
            ],
            [
            'track_name' => 'cikarang-angke',
            'station_passed' => '28,29,30,31,32,33,34,35,36,37,38,39,47,17,48,49,50,51,52,53',
            'travel_time' => '0,4,2,2,2,4,5,2,6,2,1,2,8,2,5,4,0,1,7,6,5'
            ],
            [
            'track_name' => 'bekasi-angke',
            'station_passed' => '33,34,35,36,37,38,39,47,17,48,49,50,51,52,53',
            'travel_time' => '0,5,2,6,2,1,2,8,2,5,4,0,1,7,6,5'
            ],
            [
            'track_name' => 'tanahabang-rangkasbitung',
            'station_passed' => '51,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72',
            'travel_time' => '0,5,6,8,5,2,7,3,5,4,2,5,9,3,6 ,3,5,2,9,12'
            ],
            [
            'track_name' => 'duri-tangerang',
            'station_passed' => '52,73,74,75,76,77,78,79,80,81,82',
            'travel_time' => '0,2,2,2,2,3,2,2,4,3,3'
            ],
            [
            'track_name' => 'jakartakota-tanjungpriok',
            'station_passed' => '24,72,93,94,95',
            'travel_time' => '0,4,4,4,5'
            ]
        ];

            // Trackway::create([
            // 'track_name' => 'bekasi-kampungbandan via mri',
            // 'station_passed' => '33,34,35,36,37,38,39,47,17,48,49,50,51,52,53,46',
            // 'travel_time' => '0,5,2,6,2,1,2,8,2,5,4,0,1,7,6,5,10'
            // ],);

        $lrt = [
            [
                'track_name' => 'dukuhatas-jatimulya',
                'station_passed' => '12,13,14,15,16,17,18,19,20,21,22,23,24,25',
                'travel_time' => '0,3,3,3,3,5,3,3,3,3,3,3,3,3'
            ],
            [
                'track_name' => 'dukuhatas-harjamukti
                ',
                'station_passed' => '12,13,14,15,16,17,18,19,26,27,28',
                'travel_time' => '0,3,3,3,3,5,3,3,3,3,3,3'
            ]
        ];

            foreach($train as $tr) {
                Trackway::create([
                    'track_name' => $tr['track_name'],
                    'station_passed' => $tr['station_passed'],
                    'travel_time' => $tr['travel_time']
                ]);
            }

            foreach($lrt as $rt) {
                LrtTrackway::create([
                    'track_name' => $rt['track_name'],
                    'station_passed' => $rt['station_passed'],
                    'travel_time' => $rt['travel_time']
                ]);
            }
    }
}
