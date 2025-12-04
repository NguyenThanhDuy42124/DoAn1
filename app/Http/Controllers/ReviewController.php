<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Chuyển đổi trạng thái ẩn/hiện của đánh giá
     */
    public function toggleHidden($id)
    {
        $review = Review::findOrFail($id);

        // BẢO MẬT: Chỉ người viết đánh giá mới được phép ẩn/hiện
        if (Auth::id() !== $review->buyer_id) {
            abort(403, 'Bạn không có quyền thay đổi trạng thái đánh giá này.');
        }

        // Đảo ngược trạng thái (True -> False, False -> True)
        $review->is_hidden = !$review->is_hidden;
        $review->save();

        $message = $review->is_hidden 
            ? 'Đánh giá đã được ẩn.' 
            : 'Đánh giá đã được hiển thị công khai.';

        return back()->with('success', $message);
    }
}