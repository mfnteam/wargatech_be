<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Picture;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function createReport(Request $request) {
        $user = $request->user();
        if($user->role !== 'warga') {
            return response()->json([
                'status' => 'Error',
                'message' => 'Akses ditolak, anda harus berstatus warga'
            ], 403);
        }

        $validated = Validator::make($request->all(), [
            'type' => 'required|in:infrastruktur,fasilitas,pelanggaran,lingkungan',
            'location' => 'required|string',
            'description' => 'required',
            'attachment' => 'required|max:5012'
        ]);

        if($validated->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Invalid field',
                'errors' => $validated->errors()
            ], 422);
        }

        $report = Report::create([
            'user_id' => $user->id,
            'type' => $request['type'],
            'location' => $request['location'],
            'description' => $request['description'],
            'status' => 'unfinished'
        ]);

        $image = $request->file('attachment');

        $fileurl = $image->store('images', 'public');
        Picture::create([
            'report_id' => $report->id,
            'img_url' => $fileurl
        ]);

        return response()->json([
            'status' => 'Success',
            'message' => 'Laporan berhasil dibuat, mohon tunggu hingga laporan ditanggapi oleh petugas'
        ], 201);
    }

    public function markFinish(Request $request, $id) {
        $user = $request->user();
        if($user->role !== "petugas") {
            return response()->json([
                'status' => 'Error',
                'message' => 'Forbidden access'
            ], 403);
        }

        $laporan = Report::find($id);
        if(!$laporan) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        $laporan->status = "finish";
        $laporan->save();

        return response()->json([
            'status' => 'Success',
            'message' => 'Laporan dengan id ' . $laporan->id . " berhasil diselesaikan"
        ], 200);
    }

    public function getAllReport(Request $request) {
        $user = $request->user();
        if($user->role !== "petugas") {
            return response()->json([
                'status' => 'Error',
                'message' => 'Forbidden access'
            ], 403);
        }

        $laporan = Report::with('Picture')->join('users', 'users.id', '=', 'reports.user_id')
                            ->select('reports.*', 'users.id as user_id', 'users.name')
                            ->orderBy('created_at')
                            ->get();

        return response()->json([
            'status' => 'Success',
            'message' => 'Berhasil mendapatkan laporan',
            'report' => $laporan->map(function($lpr) {
                return [
                    'id' => $lpr->id,
                    'name' => $lpr->name,
                    'type' => $lpr->type,
                    'location' => $lpr->location,
                    'description' => $lpr->description,
                    'status' => $lpr->status,
                    'attachment' => Picture::where('report_id', $lpr->id)->first('img_url'),
                    'created_at' => date_format(Carbon::parse($lpr->created_at)->addHours(7), 'Y-m-d H:i')
                ];
            })
        ]);
    }


    public function getUserReport(Request $request) {
        $user = $request->user();
        if($user->role !== 'warga') {
            return response()->json([
                'status' => "Error",
                'message' => 'Akses ditolak, anda harus berstatus warga'
            ], 403);
        }

        $laporan = Report::with('Picture')->where('user_id', $user->id)
                            ->join('users', 'users.id', '=', 'reports.user_id')
                            ->select('reports.*', 'users.id as user_id', 'users.name')
                            ->orderBy('created_at')
                            ->get();

        return response()->json([
            'status' => 'Success',
            'message' => 'Berhasil mendapatkan laporan',
            'report' => $laporan->map(function($lpr) {
                return [
                    'id' => $lpr->id,
                    'name' => $lpr->name,
                    'type' => $lpr->type,
                    'location' => $lpr->location,
                    'description' => $lpr->description,
                    'status' => $lpr->status,
                    'attachment' => Storage::url(Picture::where('report_id', $lpr->id)->first()->img_url),
                    'created_at' => date_format(Carbon::parse($lpr->created_at)->addHours(7), 'Y-m-d H:i')
                ];
            })
        ]);
    }

    public function deleteReport(Request $request, $id) {
        $user = $request->user();
        if($user->role !== 'warga') {
            return response()->json([
                'status' => "Error",
                'message' => 'Akses ditolak, anda harus berstatus warga'
            ], 403);
        }

        $report = Report::find($id);
        if(!$report) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        Report::destroy($id);
        return response()->json([
            'status' => 'Success',
            'message' => 'Laporan berhasil dihapus'
        ]);
    }
}
