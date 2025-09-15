<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
 public function index()
    {
        // Lấy danh sách thông báo (nếu có lưu DB)
        // $notifications = Notification::all();
        // return view('admin.notifications.index', compact('notifications'));

        return view('admin.notifications.index'); // Tạm thời chỉ trả về view
    }

    public function store(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Code lưu thông báo vào DB hoặc gửi đi
        // Notification::create($request->all());

        return redirect()->route('admin.notifications.index')
                         ->with('success', 'Thông báo đã được gửi thành công!');
    }}
