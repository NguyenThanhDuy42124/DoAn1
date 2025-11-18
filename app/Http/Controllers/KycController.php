<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function show($type, $userId)
    {
        $user = User::findOrFail($userId);

        // Chỉ admin hoặc chính chủ được xem
        if (auth()->guest() || (auth()->id() !== $user->id && (auth()->user()->role ?? '') !== 'admin')) {
            abort(403);
        }

        $path = match($type) {
            'front' => $user->cccd_front_image_path ?? null,
            'back'  => $user->cccd_back_image_path  ?? null,
            'selfie'=> $user->cccd_selfie_image_path ?? null,
            default => null,
        };

        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $fullPath = Storage::disk('local')->path($path);

        return response()->file($fullPath, [
            'Cache-Control' => 'private, max-age=600, must-revalidate',
        ]);
    }
}
