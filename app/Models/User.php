<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Table(incrementing: true, timestamps: true)]
#[Fillable(['name', 'email', 'password', 'nik', 'birthday', 'nomor_kk', 'role', 'gender', 'phone'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    public function Medical() {
        return $this->hasMany(Medical::class, 'user_id', 'id');
    }

    public function Report() {
        return $this->hasMany(Report::class, 'user_id', 'id');
    }

    public function VerificationCode() {
        return $this->hasOne(CodeVerification::class, 'user_id', 'id');
    }

    public function ProfilePicture() {
        return $this->hasOne(ProfilePictures::class, 'user_id', 'id');
    }
}
