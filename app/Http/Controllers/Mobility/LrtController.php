<?php

namespace App\Http\Controllers\Mobility;

use App\Http\Controllers\Controller;
use App\Models\LrtRoute;
use App\Models\LrtTrackway;
use App\Models\LrtTrain;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function Illuminate\Support\now;

class LrtController extends Controller
{
    public function createLrt(Request $request) {
        $user = $request->user();

        if($user->role !== 'petugas') {
            return response()->json([
                'status' => 'Error',
                'message' => 'Akses ditolak'
            ], 403);
        }


        $validated = Validator::make($request->all(), [
            'code' => 'required',
            'departure' => 'required|date_format:H:i|after_or_equal:06:00|before_or_equal:22:00',
            'type' => 'required|in:jabodebek,jakarta',
            'stasiun_awal' => [Rule::requiredIf($request['type'] == "jabodebek")],
            'stasiun_akhir' => [Rule::requiredIf($request['type'] == "jabodebek")],
            'destination' => [
                    Rule::requiredIf($request['type'] === "jakarta", Rule::in(['pegangsaandua', 'manggarai'])),
            ]
        ]);

        if($validated->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Invalid field',
                'errors' => $validated->errors()
            ], 422);
        }

        if($request['type'] === "jakarta") {
            $train = LrtTrain::create([
            'code' => $request['code'],
            'departure' => $request['departure'],
            'destination' => $request['destination'],
            'type' => $request['type']
            ]);
            if(strtolower($request['destination']) === "manggarai") {
                for($st = 1; $st <= 11; $st++) {
            if($st === 1) {
            LrtRoute::create([
                'train_id' => $train->id,
                'station_id' => $st,
                'travel_time' => 0,
                'order' => $st
            ]);
            } else {
            LrtRoute::create([
                'train_id' => $train->id,
                'station_id' => $st,
                'travel_time' => 3,
                'order' => $st
            ]);
            }
                }
            return response()->json([
                'status' => "Success",
                'message' => 'Kereta berhasil dibuat'
            ]);
            }


            if(strtolower($request['destination']) === "pegangsaandua") {
                $i = 1;
                for($st = 11; $st >= 1; $st--) {
            if($st === 11) {
            LrtRoute::create([
                'train_id' => $train->id,
                'station_id' => $st,
                'travel_time' => 0,
                'order' => $i
            ]);
            } else {
            LrtRoute::create([
                'train_id' => $train->id,
                'station_id' => $st,
                'travel_time' => 3,
                'order' => $i
            ]);
            }
                $i += 1;
                }

            return response()->json([
                'status' => "Success",
                'message' => 'Kereta berhasil dibuat'
            ]);
            }

        return response()->json([
            'status' => 'Error',
            'message' => 'unknown route'
        ]);
        }


        if($request['type'] === "jabodebek") {
            $lrttrack = strtolower($request['stasiun_awal']) . strtolower($request['stasiun_akhir']);

            $train = LrtTrain::create([
                'code' => $request['code'],
                'departure' => $request['departure'],
                'destination' => $request['stasiun_akhir'],
                'type' => $request['type']
                ]);

            if($lrttrack === "dukuhatasjatimulya") {

                $dukuhataspg2 = LrtTrackway::get();
                $dukuhataspg2 = $dukuhataspg2[0];

                lrt_track_maker($dukuhataspg2, $train);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ]);
            }


            if($lrttrack === "jatimulyadukuhatas") {

                $dukuhataspg2 = LrtTrackway::get();
                $dukuhataspg2 = $dukuhataspg2[0];

                lrt_reverse_track_maker($dukuhataspg2, $train);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat',
                    'route' => LrtRoute::where('train_id', $train->id)->get()
                ]);
            }


            if($lrttrack === "dukuhatasharjamukti") {

                $dukuhataspg2 = LrtTrackway::get();
                $dukuhataspg2 = $dukuhataspg2[1];

                lrt_track_maker($dukuhataspg2, $train);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ]);
            }

            if($lrttrack === "harjamuktidukuhatas") {

                $dukuhataspg2 = LrtTrackway::get();
                $dukuhataspg2 = $dukuhataspg2[1];

                lrt_reverse_track_maker($dukuhataspg2, $train);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat',
                    'route' => LrtRoute::where('train_id', $train->id)->get()
                ]);
            }

            LrtTrain::destroy($train->id);
            return response()->json([
            'status' => 'Error',
            'message' => 'unknown route'
            ], 404);
        }
    }


    public function showLrt(Request $request) {

        $type = $request->query('type');

        $train = LrtTrain::with('LrtRoute')->where('type', $type)
                            ->orderBy('departure')->get();

        return response()->json([
            'status' => 'Success',
            'message' => 'Berhasil mendapatkan kereta',
            'train' => $train->map(function($train) {
                return [
                    'id' => $train->id,
                    'code' => $train->code,
                    'type' => $train->type,
                    'destination' => $train->destination,
                    'departure' => date_format(Carbon::parse($train->departure), 'H:i')
                ];
            })
        ]);
    }


    public function detailLrt($id) {
        $train = LrtTrain::where('id', $id)->get();

        if(!$train) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Kereta tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Berhasil mendapatkan kereta',
            'train' => $train->map(function($lrt) {
                $dept = Carbon::parse($lrt->departure);
                return [
                'id' => $lrt->id,
                'code' => $lrt->code,
                'destination' => $lrt->destination,
                'type' => 'LRT ' . $lrt->type,
                'departure' => date_format(Carbon::parse($lrt->departure), 'H:i'),
                'station' => LrtRoute::where('train_id', $lrt->id)
                                ->join('lrt_stations', 'lrt_stations.id', '=', 'lrt_routes.station_id')
                                ->orderBy('order')
                                ->get()->map(function($st) use($dept) {
                                    return [
                                        'name' => $st->name,
                                        'time' => date_format($dept->addMinutes($st->travel_time), 'H:i')
                                    ];
                                })
            ];
            })
        ]);

    }
}
