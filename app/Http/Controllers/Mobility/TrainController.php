<?php

namespace App\Http\Controllers\Mobility;

use App\Http\Controllers\Controller;
use App\Models\RouteOrder;
use App\Models\Trackway;
use App\Models\Train;
use App\Models\TrainRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TrainController extends Controller
{
    public function createTrain(Request $request) {
        $user = $request->user();
        if($user->role !== "petugas") {
            return response()->json([
                'status' => 'Error',
                'message' => 'Akses ditolak'
            ], 403);
        }

        $validated = Validator::make($request->all(), [
            'code' => 'required',
            'departure' => 'required|date_format:H:i',
            'line' => 'required|in:redline,greenline,blueline,purpleline,brownline',
            'stasiun_awal' => 'required',
            'stasiun_akhir' => 'required',
            'via' => [
                Rule::requiredIf(($request->stasiun_awal === 'cikarang' && $request->stasiun_akhir === "kampungbandan") || ($request->stasiun_awal === 'bekasi' && $request->stasiun_akhir === "kampungbandan") || ($request->stasiun_awal === 'kampungbandan' && $request->stasiun_akhir === "cikarang") || ($request->stasiun_awal === 'kampungbandan' && $request->stasiun_akhir === "bekasi")),
                Rule::in(['mri', 'pse']),
                    ],
        ], [
            'via.in' => 'Tolong pilih antara pse (Pasar Senen) atau mri (Manggarai)'
        ]);


        if($validated->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Invalid Field',
                'errors' => $validated->errors()
            ], 422);
        }

        $train = Train::create([
            'code' => $request['code'],
            'departure' => $request['departure']
        ]);

        $track = Trackway::get();

        //redline
        if($request['line'] === "redline") {
            $redlinetrack = strtolower($request['stasiun_awal']) . strtolower($request['stasiun_akhir']);
            if($redlinetrack === "bogorjakartakota") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $bogorjakarta = $track[0];
                track_maker($bogorjakarta, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }

            if($redlinetrack === "jakartakotabogor") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $bogorjakarta = $track[0];
                reverse_track_maker($bogorjakarta, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }

            if($redlinetrack === "nambojakartakota") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $nambojakarta = $track[1];
                track_maker($nambojakarta, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }

            if($redlinetrack === "jakartakotanambo") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $nambojakarta = $track[1];
                reverse_track_maker($nambojakarta, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }
        }


        //blue line
        if($request['line'] === "blueline") {
            $bluelinetrack = strtolower($request['stasiun_awal']) . strtolower($request['stasiun_akhir']);

            //via pasar senen
            if(($request['via'] === "pse")) {

                if($bluelinetrack === "cikarangkampungbandan") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir']) . " via PSE"
                ]);

                $cikarangkpbandan = $track[2];
                track_maker($cikarangkpbandan, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
                }
                
                if($bluelinetrack === "kampungbandancikarang") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir']) . " via PSE"
                ]);

                $cikarangkpbandan = $track[2];
                reverse_track_maker($cikarangkpbandan, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
                }


                if($bluelinetrack === "bekasikampungbandan") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir']) . " via PSE"
                ]);

                $cikarangkpbandan = $track[3];
                track_maker($cikarangkpbandan, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
                }

                if($bluelinetrack === "kampungbandanbekasi") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir']) . " via PSE"
                ]);

                $cikarangkpbandan = $track[3];
                reverse_track_maker($cikarangkpbandan, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
                }
            }


            //manggarai
            if(($request['via'] === "mri")) {

                if($bluelinetrack === "cikarangkampungbandan") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir']) . " via MRI"
                ]);

                $cikarangkpbandan = $track[4];
                track_maker($cikarangkpbandan, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
                }
                

                if($bluelinetrack === "kampungbandancikarang") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir']) . " via MRI"
                ]);

                $cikarangkpbandan = $track[4];
                reverse_track_maker($cikarangkpbandan, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
                }


                if($bluelinetrack === "bekasikampungbandan") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir']) . " via MRI"
                ]);

                $bekasikpbandan = $track[5];
                track_maker($bekasikpbandan, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
                }
                

                if($bluelinetrack === "kampungbandanbekasi") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir']) . " via MRI"
                ]);

                $kpbandanbekasi = $track[5];
                reverse_track_maker($kpbandanbekasi, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
                }
            }

            //ckrg
            if($bluelinetrack === "cikarangangke") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $cikarangangke = $track[6];
                track_maker($cikarangangke, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }

            if($bluelinetrack === "angkecikarang") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $cikarangangke = $track[6];
                reverse_track_maker($cikarangangke, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }

            //bks
            if($bluelinetrack === "bekasiangke") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $cikarangangke = $track[7];
                track_maker($cikarangangke, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }

            if($bluelinetrack === "angkebekasi") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $cikarangangke = $track[7];
                reverse_track_maker($cikarangangke, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }
        }


        //greenline
        if($request['line'] === "greenline") {
            $greenlinetrack = strtolower($request['stasiun_awal']) . strtolower($request['stasiun_akhir']);
            if($greenlinetrack === "tanahabangrangkasbitung") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $tnabangrangkas = $track[8];
                track_maker($tnabangrangkas, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }

            if($greenlinetrack === "rangkasbitungtanahabang") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $tnabangrangkas = $track[8];
                reverse_track_maker($tnabangrangkas, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }
        }


        //purpleline
        if($request['line'] === "purpleline") {
            $purplelinetrack = strtolower($request['stasiun_awal']) . strtolower($request['stasiun_akhir']);
            if($purplelinetrack === "jakartakotatanjungpriok") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $jktkotatjpriok = $track[10];
                track_maker($jktkotatjpriok, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }

            if($purplelinetrack === "tanjungpriokjakartakota") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $jktkotatjpriok = $track[10];
                reverse_track_maker($jktkotatjpriok, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }
        }


        //brownline
        if($request['line'] === "brownline") {
            $brownlinetrack = strtolower($request['stasiun_awal']) . strtolower($request['stasiun_akhir']);
            if($brownlinetrack === "duritangerang") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $duritangerang = $track[9];
                track_maker($duritangerang, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }

            if($brownlinetrack === "tangerangduri") {
                $route = TrainRoute::create([
                    'train_id' => $train->id,
                    'name' => $request['line'],
                    'direction' => strtolower($request['stasiun_awal']) . "-" . strtolower($request['stasiun_akhir'])
                ]);

                $duritangerang = $track[9];
                reverse_track_maker($duritangerang, $route);

                return response()->json([
                    'status' => 'Success',
                    'message' => 'Kereta berhasil dibuat'
                ], 201);
            }
        }

        Train::destroy($train->id);

        return response()->json([
            'status' => 'Error',
            'message' => 'Unknown Route'
        ], 404);
    }


    public function listTrain(Request $request) {
        $train = Train::with('Route')->get();

        return response()->json([
            'status' => 'Success',
            'message' => 'Berhasil mendapatkan kereta',
            'train' => $train->map(function($train) {
                return [
                    'id' => $train->id,
                    'code' => $train->code,
                    'line' => $train->route[0]->name,
                    'direction' => $train->route[0]->direction,
                    'departure' => date_format(Carbon::parse($train->departure), 'H:i')
                ];
            })
        ]);
    }

    public function detailTrain($id) {
        $train = Train::find($id);
        if(!$train) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Kereta tidak ditemukan'
            ], 404);
        }

        $route = TrainRoute::where('train_id', $id)->first();
        $order = RouteOrder::where('route_id', $route->id)
                            ->join('train_stations', 'train_stations.id', '=', 'route_orders.station_id')
                            ->orderBy('order')
                            ->get();

        $departure = Carbon::parse($train->departure);
        $result = [];

        foreach($order as $routes) {
            $departure->addMinutes($routes->travel_time);

            $result[] = [
                'station' => $routes->name,
                'time' => $departure->format('H:i')
            ];
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'Mendapatkan detail kereta berhasil',
            'code' => $train->code,
            'direction' => $route->direction,
            'station' => $result
        ]);
    }
}
