<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class InvitationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'is_active',
        'is_used',
    ];

    // Method yang digunakan untuk mengenerate kode undangan
    public static function generateCode()
    {
        return strtoupper(Str::random(10)); // Kode akan berbentuk string random dalam 10 karakter
    }
}
