<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
class FollowController extends Controller
{
    /**
     * Xử lý việc theo dõi hoặc bỏ theo dõi một seller.
     */
   public function toggleFollow(User $seller)
    {
        $user = Auth::user(); // $user là người đi theo dõi

        // **CODE MỚI:** Ngăn người dùng tự theo dõi chính mình
        if ($user->id === $seller->id) {
            return redirect()->back()->with('error', 'Bạn không thể tự theo dõi chính mình.');
        }

        $isFollowing = $user->following()->where('seller_id', $seller->id)->exists();

        if ($isFollowing) {
            // Nếu đang theo dõi -> Bỏ theo dõi (detach)
            $user->following()->detach($seller->id);
            $message = 'Đã bỏ theo dõi ' . $seller->name;
        } else {
            // Nếu chưa theo dõi -> Theo dõi (attach)
            $user->following()->attach($seller->id);
            
            // ===== CODE MỚI: Gửi thông báo cho Seller =====
            Notification::create([
                'user_id' => $seller->id, // Gửi thông báo CHO seller
                'type'    => 'new_follower',
                'message' => $user->name . ' đã bắt đầu theo dõi bạn.',
                'data'    => json_encode([
                    'follower_id'   => $user->id, // Lưu ID người vừa theo dõi
                    'follower_name' => $user->name
                ]),
                'is_read' => false,
            ]);
            // ===== KẾT THÚC CODE MỚI =====

            $message = 'Đã theo dõi ' . $seller->name;
        }

        // Trả về thông báo thành công cho người vừa nhấn nút
        return redirect()->back()->with('success', $message);
    }
}