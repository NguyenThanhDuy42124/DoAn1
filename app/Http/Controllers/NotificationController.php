<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Hiển thị danh sách các thông báo - tự động phân biệt admin/user
     */
    public function index()
    {
        // Kiểm tra nếu đang truy cập route admin
        if (request()->routeIs('admin.*')) {
            // Chỉ admin mới được truy cập
            if (Auth::user()->role !== 'admin') {
                abort(403);
            }
            
            $notifications = Notification::with('user')->latest()->paginate(20);
            return view('admin.notifications.index', compact('notifications'));
        }
        
        // Cho user thông thường
        $notifications = Notification::where('user_id', Auth::id())->latest()->paginate(5);
        
        // Đánh dấu tất cả là đã đọc khi vào trang (chỉ cho user thông thường)
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return view('pages.notifications', compact('notifications'));
    }

    /**
     * Đánh dấu thông báo là đã đọc - xử lý cả admin và user
     */
    public function markAsRead(Notification $notification)
    {
        // Kiểm tra quyền truy cập
        if (Auth::user()->role !== 'admin' && $notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Thông báo đã được đánh dấu là đã đọc!');
    }

    /**
     * Đánh dấu thông báo là chưa đọc - xử lý cả admin và user
     */
    public function markAsUnread(Notification $notification)
    {
        // Kiểm tra quyền truy cập
        if (Auth::user()->role !== 'admin' && $notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => false]);

        return redirect()->back()->with('success', 'Thông báo đã được đánh dấu là chưa đọc!');
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc (cho user thông thường)
     */
    public function markAllAsRead()
    {
        // User thông thường chỉ đánh dấu thông báo của chính mình
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return redirect()->back()->with('success', 'Tất cả thông báo đã được đánh dấu là đã đọc!');
    }

    /**
     * Xóa thông báo khỏi cơ sở dữ liệu.
     */
    public function destroy(Notification $notification)
    {
        // Kiểm tra quyền truy cập
        if (Auth::user()->role !== 'admin' && $notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        return redirect()->back()->with('success', 'Thông báo đã được xóa thành công!');
    }

    // ==================== ADMIN ONLY METHODS ====================

    /**
     * Hiển thị form tạo mới thông báo (Admin only).
     */
    public function create()
    {
        // Chỉ admin mới được truy cập
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.notifications.create');
    }

    /**
     * Lưu thông báo mới vào cơ sở dữ liệu (Admin only).
     */
    public function store(Request $request)
    {
        // Chỉ admin mới được truy cập
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:255',
            'message' => 'required|string',
            'is_read' => 'boolean'
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
        // Kiểm tra quyền truy cập
        if (Auth::user()->role !== 'admin' && $notification->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Admin xem chi tiết trong admin view, user xem trong user view
        if (Auth::user()->role === 'admin') {
            return view('admin.notifications.show', compact('notification'));
        }
        
        return view('notifications.show', compact('notification'));
    }

    /**
     * Hiển thị form chỉnh sửa thông báo (Admin only).
     */
    public function edit(Notification $notification)
    {
        // Chỉ admin mới được chỉnh sửa thông báo
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        
        return view('admin.notifications.edit', compact('notification'));
    }

    /**
     * Cập nhật thông báo trong cơ sở dữ liệu (Admin only).
     */
    public function update(Request $request, Notification $notification)
    {
        // Chỉ admin mới được cập nhật thông báo
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:255',
            'message' => 'required|string',
            'is_read' => 'boolean'
        ]);

        $notification->update($validated);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Thông báo đã được cập nhật thành công!');
    }
}