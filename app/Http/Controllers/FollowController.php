<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Xử lý việc theo dõi hoặc bỏ theo dõi một seller.
     */
    public function toggleFollow(User $seller)
    {
        // $seller được tự động tìm thấy từ route model binding
        $user = Auth::user();

        // Kiểm tra xem user có đang theo dõi seller này không
        $isFollowing = $user->following()->where('seller_id', $seller->id)->exists();

        if ($isFollowing) {
            // Nếu đang theo dõi -> Bỏ theo dõi (detach)
            $user->following()->detach($seller->id);
            $message = 'Đã bỏ theo dõi ' . $seller->name;
        } else {
            // Nếu chưa theo dõi -> Theo dõi (attach)
            $user->following()->attach($seller->id);
            $message = 'Đã theo dõi ' . $seller->name;
        }

        return redirect()->back()->with('success', $message);
    }
}