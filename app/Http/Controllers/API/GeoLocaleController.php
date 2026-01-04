<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeoLocaleController extends Controller
{
    public function detect(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $lat = $request->latitude;
        $lon = $request->longitude;

        // Default to English
        $locale = 'en';

        // Approximate Bounding Boxes / Centers
        // Vietnam: Lat 8-24, Lon 102-110
        if ($lat >= 8 && $lat <= 24 && $lon >= 102 && $lon <= 110) {
            $locale = 'vi';
        }
        // Germany: Lat 47-55, Lon 5-16
        elseif ($lat >= 47 && $lat <= 55 && $lon >= 5 && $lon <= 16) {
            $locale = 'de';
        }
        // Korea (South): Lat 33-39, Lon 124-131
        elseif ($lat >= 33 && $lat <= 39 && $lon >= 124 && $lon <= 131) {
            $locale = 'kr';
        }

        // Store in session
        session(['locale' => $locale]);

        // If user is logged in, update their preference if it's not set or they want auto-detection (optional logic)
        // For now, we only set session for guests or override session. 
        // User preference in DB usually takes precedence, but if they haven't set it explicitly, we might suggest it.
        // The prompt says "update the users.display_language if logged in".
        if (Auth::check()) {
            $user = Auth::user();
            // Optional: Only update if current setting is default 'en' and we detected something else? 
            // Or always update? The prompt says "Action: Set the app.locale session/cookie and update the users.display_language if logged in."
            // We will update it.
            $user->display_language = $locale;
            $user->save();
        }

        return response()->json([
            'locale' => $locale,
            'message' => 'Locale detected and set to ' . $locale
        ]);
    }
}
