<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\CodeVerification;
use App\Models\ProfilePictures;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use function Illuminate\Support\now;

class ProfileController extends Controller
{
    public function changeProfile(Request $request) {
        $user = $request->user();

        if($user->first()->email_verified_at <= now()) {
            $otp = CodeVerification::where('user_id', $user->first()->id)->first();
            $otp->delete();

            $newotp = Str::random(8);

            CodeVerification::create([
                'user_id' => $user->first()->id,
                'code' => $newotp,
                'expired_at'=> now()->addMinutes(5)
            ]);

            Mail::to($user->first()->email)->send(new VerificationCodeMail($newotp));

            return response()->json([
                'status' => 'Error',
                'message' => 'Mohon verifikasi terlebih dahulu'
            ], 422);
        }

        $validated = Validator::make($request->all(), [
            'name' => 'required|regex:/^[A-Za-z ]+$/|min:3',
            'gender' => 'required|in:male,female',
            'phone' => 'required|int|min:12|max:16',
            'birthday' => 'required|date_format:Y-m-d',
            'nik' => 'required|int|unique:users,nik|min:16|max:16',
            'nomor_kk' => 'required|int'
        ], [
            'name.regex' => 'Invalid character input, please use alphabet only',
            'gender.in' => 'Mohon pilih jenis kelamin yang benar'
        ]);

        if($validated->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Invalid field',
                'errors' => $validated->errors()
            ], 422);
        }

        User::find($user->id)->update([
            'name' => $request['name'],
            'birthday' => $request['birthday'],
            'phone' => $request['phone'],
            'nik' => $request['nik'],
            'nomor_kk' => $request['nomor_kk'],
        ]);
        return response()->json([
            'status' => 'Success',
            'message' => "Profil berhasil diubah!"
        ]);
    }


    public function changePhoto(Request $request) {
        $user = $request->user();
        $validated = Validator::make($request->all(), [
            'attachment' => 'required|max:2048|images'
        ]);

        if($validated->fails()) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Invalid field',
                'error' => $validated->errors()
            ], 422);
        }

        if($request->hasFile('attachment')) {
            $path = $request->file('attachment');
            $profpic = ProfilePictures::where('user_id', $user->id)->first();
            if($profpic) {
                $profpic->delete();
            }

            ProfilePictures::create([
                'user_id' => $user->id,
                'img' => $path->store('profile', 'public')
            ]);

            return response()->json([
                'status' => 'Success',
                'message' => 'Berhasil mengganti foto profil'
            ]);
        }

        return response()->json([
            'status' => 'Error',
            'message' => 'mohon kirimkan gambar'
        ], 422);
    }


    public function deletePhoto(Request $request) {
        $user = $request->user();

        $profpic = ProfilePictures::where('user_id', $user->id)->first();
        $profpic->img = null;
        $profpic->save();

        return response()->json([
            'status' => 'Success',
            'message' => 'Berhasil menghapus foto profil'
        ]);
    }
}
