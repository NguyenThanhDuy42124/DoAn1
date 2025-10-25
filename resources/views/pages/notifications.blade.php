@extends('layouts.account')

@section('title', 'Thông báo của tôi')

@push('styles')
<style>
    /* CSS tùy chỉnh cho trang thông báo */
    .notification-item.unread {
        /* Màu nền sáng cho thông báo chưa đọc */
        background-color: #f8f9fa; 
    }
    .notification-item {
        transition: background-color 0.2s ease-in-out;
        border-bottom: 1px solid #dee2e6;
    }
    .notification-item:last-child {
        border-bottom: none; /* Xóa border cho item cuối cùng */
    }
    .notification-item:hover {
        /* Màu nền khi hover */
        background-color: #eef2f7; 
    }
    .notification-icon {
        font-size: 1.5rem; /* Kích thước icon */
        width: 40px;
        text-align: center;
    }
    /* Tùy chỉnh nút dạng icon */
    .notification-item .btn-link {
        text-decoration: none;
        padding: 0.25rem 0.5rem;
        color: #6c757d;
        transition: all 0.2s;
    }
    .notification-item .btn-link:hover {
        color: #343a40;
        transform: scale(1.1);
    }
    .notification-item .btn-link .text-danger:hover {
        color: #dc3545 !important;
    }
    .notification-item .btn-link .text-success:hover {
        color: #198754 !important;
    }
</style>
@endpush

@section('account_content')

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Thông báo của tôi</h5>
        @if($notifications->count() > 0)
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc
                </button>
            </form>
        @endif
    </div>

    {{-- Xóa padding của card-body để list item đẹp hơn --}}
    <div class="card-body p-0"> 
        @if(session('success'))
            {{-- Đặt thông báo session bên ngoài card-body.p-0 cho đẹp --}}
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($notifications->count() > 0)
            <div class="notification-list">
                @foreach($notifications as $notification)
                    <div class="notification-item d-flex align-items-start p-3 {{ $notification->is_read ? 'read' : 'unread' }}">
                        
                        {{-- Icon --}}
                        <div class="flex-shrink-0 me-3 pt-1">
                            <span class="notification-icon {{ $notification->is_read ? 'text-muted' : 'text-primary' }}">
                                <i class="fas fa-bell"></i>
                            </span>
                        </div>
                        
                        {{-- Content --}}
                        <div class="flex-grow-1">
                            <p class="mb-1 {{ $notification->is_read ? 'text-muted' : 'fw-bold' }}">
                                {{ $notification->message }}
                            </p>
                            <small class="text-muted">
                                <i class="far fa-clock"></i>
                                {{ $notification->created_at->format('d/m/Y H:i') }}
                                ({{ $notification->created_at->diffForHumans() }})
                            </small>
                        </div>
                        
                        {{-- Actions (Nút bấm dạng icon) --}}
                        <div class="flex-shrink-0 ms-3">
                            <div class="btn-group" role="group">
                                @if(!$notification->is_read)
                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link" title="Đánh dấu đã đọc">
                                            <i class="fas fa-check-circle text-success fs-5"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('notifications.markAsUnread', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link" title="Đánh dấu chưa đọc">
                                            <i class="far fa-circle text-muted fs-5"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa thông báo này?')">
                                        <i class="fas fa-trash-alt text-danger fs-5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Đưa phân trang vào card-footer cho đẹp --}}
            @if ($notifications->hasPages())
                <div class="card-footer bg-white">
                    {{ $notifications->links() }}
                </div>
            @endif
            
        @else
            {{-- Giao diện khi không có thông báo --}}
            <div class="text-center py-5">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không có thông báo nào.</h5>
                <p class="text-muted mb-0">Tất cả thông báo của bạn sẽ xuất hiện ở đây.</p>
            </div>
        @endif
    </div>
</div>

@endsection