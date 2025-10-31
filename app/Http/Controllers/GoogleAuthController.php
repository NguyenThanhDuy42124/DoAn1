<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $avatarUrl = $googleUser->getAvatar();
            $savedImagePath = null; // Biến để lưu đường dẫn ảnh đã lưu

            // ----------------------------------------------------
            //  Tải ảnh về và lưu vào storage
            // ----------------------------------------------------
            if ($avatarUrl) {
                try {
                    // 1. Tải nội dung ảnh từ URL của Google
                    $imageContents = file_get_contents($avatarUrl);

                    if ($imageContents) {
                        // 2. Tạo một tên file ngẫu nhiên, duy nhất
                        // Giả sử ảnh là .jpg (phổ biến nhất cho avatar)
                        $imageName = 'profile_images/' . Str::random(40) . '.jpg';

                        // 3. Lưu file vào disk 'public' (tức là storage/app/public)
                        Storage::disk('public')->put($imageName, $imageContents);

                        // 4. Lưu lại đường dẫn để cập nhật vào database
                        // (Lưu ý: chúng ta chỉ lưu đường dẫn tương đối)
                        $savedImagePath = $imageName;
                    }
                } catch (\Exception $e) {
                    // Nếu tải ảnh bị lỗi (ví dụ: Google URL hỏng),
                    // $savedImagePath sẽ vẫn là null và code chạy tiếp bình thường
                    // Bạn có thể log lỗi ở đây nếu muốn:
                    // Log::error('Lỗi tải avatar Google: ' . $e->getMessage());
                }
            }
            // ----------------------------------------------------
            // Kết thúc tải và lưu ảnh
            // ----------------------------------------------------

            // Tìm hoặc tạo User
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // User đã tồn tại (ví dụ: đăng ký bằng email/pass trước đó)
                $user->update([
                    'google_id' => $googleUser->getId(),

                    // [THAY ĐỔI] Chỉ cập nhật 'img' nếu họ chưa có ảnh
                    // (Toán tử '??' nghĩa là: nếu $user->img đã có giá trị thì dùng nó,
                    // nếu $user->img là null thì dùng $savedImagePath)
                    'img'       => $user->img ?? $savedImagePath,
                ]);
            } else {
                // User mới, tạo user với thông tin từ Google
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),

                    // [THAY ĐỔI] Gán đường dẫn ảnh đã lưu vào cột 'img'
                    'img'               => $savedImagePath,

                    // Gán các giá trị mặc định cho database của bạn
                    'status'            => 'active',
                    'role'              => 'buyer', // Hoặc 'customer' tùy bạn
                    'password'          => null, // Mật khẩu là null
                ]);
            }

            // Đăng nhập người dùng
            Auth::login($user);

            // Chuyển hướng
            return redirect()->intended('/'); // Sửa đường dẫn nếu cần

        } catch (\Throwable $th) {
            // Bật dòng này khi dev để xem lỗi chi tiết
            throw $th;

        }
    }
}
