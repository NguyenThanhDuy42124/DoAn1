<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{



    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Authorization: Only allow editing own profile or if admin
        if ($user->id !== Auth::user()->id) {
            return redirect()->route('dashboard')->with('error', 'You can only edit your own profile.');
        }

        return view('general.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Authorization same as above
        if ($user->id !== Auth::user()->id) {
            return redirect()->route('dashboard')->with('error', 'You can only edit your own profile.');
        }

        // Validate input (add more rules as needed)
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phoneNumber' => 'nullable|string|max:15',
            'address' => 'required|string|max:255',
            'dateOfBirth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'img' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg', // Optional profile image
            'cccd_front_image' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg',
            'cccd_back_image' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg',
            'selfie_image' => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg',
        ]);
        if ($request->hasFile('img')) {

            $imagePath = $request->file('img')->store('profile_images', 'public');
            $validated['img'] = $imagePath;
        }
        // Đếm số file upload
        $count = 0;
        if ($request->hasFile('cccd_front_image')) $count++;
        if ($request->hasFile('cccd_back_image')) $count++;
        if ($request->hasFile('selfie_image')) $count++;

        // Nếu đã upload 1 hoặc 2 thì phải upload đủ cả 3
        if ($count > 0 && $count < 3) {
            return redirect()->back()->withInput()->with('error', 'Bạn phải upload đủ cả 3 ảnh CCCD (mặt trước, mặt sau và ảnh selfie)');
        }
        // ...existing code lưu ảnh...
        if ($count === 3) {
            $validated['cccd_front_image_path'] = $request->file('cccd_front_image')->store('ekyc_images', 'local');
            $validated['cccd_back_image_path'] = $request->file('cccd_back_image')->store('ekyc_images', 'local');
            $validated['cccd_selfie_image_path'] = $request->file('selfie_image')->store('ekyc_images', 'local');
            $user->ekyc_status = 'pending';
            Notification::create([
            'user_id' => $user->id,
            'type' => 'ekyc_submitted',
            'message' => 'Bạn đã gửi yêu cầu eKYC. Vui lòng chờ xác minh từ quản trị viên.',
        ]);
        }

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->input('password'));
        }


        // Update only allowed fields (no password or role)
        $user->update($validated);

        return redirect()->route('dashboard')->with('message', 'Cập nhật thành công');
    }
}
