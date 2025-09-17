<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Hiển thị danh sách các thông báo.
     */
    public function index()
    {
        $notifications = Notification::with('user')->latest()->get();
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Hiển thị form tạo mới thông báo.
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Lưu thông báo mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:255',
            'message' => 'required|string',
            'read' => 'boolean'
        ]);

        Notification::create($validated);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Thông báo đã được tạo thành công!');
    }

    /**
     * Hiển thị chi tiết một thông báo cụ thể.
     */
    public function show(Notification $notification)
    {
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Hiển thị form chỉnh sửa thông báo.
     */
    public function edit(Notification $notification)
    {
        return view('admin.notifications.edit', compact('notification'));
    }

    /**
     * Cập nhật thông báo trong cơ sở dữ liệu.
     */
    public function update(Request $request, Notification $notification)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:255',
            'message' => 'required|string',
            'read' => 'boolean'
        ]);

        $notification->update($validated);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Thông báo đã được cập nhật thành công!');
    }

    /**
     * Xóa thông báo khỏi cơ sở dữ liệu.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Thông báo đã được xóa thành công!');
    }

    /**
     * Đánh dấu thông báo là đã đọc.
     */
    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();

        return redirect()->back()
            ->with('success', 'Thông báo đã được đánh dấu là đã đọc!');
    }

    /**
     * Đánh dấu thông báo là chưa đọc.
     */
    public function markAsUnread(Notification $notification)
    {
        $notification->markAsUnread();

        return redirect()->back()
            ->with('success', 'Thông báo đã được đánh dấu là chưa đọc!');
    }
}
