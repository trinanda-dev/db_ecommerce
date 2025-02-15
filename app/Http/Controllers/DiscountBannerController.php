<?php

namespace App\Http\Controllers;

use App\Models\DiscountBanner;
use Illuminate\Http\Request;

class DiscountBannerController extends Controller
{
    /**
     * Mengembalikan seluruh discount banner
     */
    public function index()
    {
        $banners = DiscountBanner::all();
        return response()->json([
            'success' => true,
            'data' => $banners,
        ]);
    }
}
