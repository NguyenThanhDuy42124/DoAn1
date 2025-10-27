@extends('layouts.account')

@section('title', 'Thông báo của tôi')

@push('styles')
<style>
    /* CSS tùy chỉnh cho trang thông báo */
    .notification-item.unread {
        background-color: #f8f9fa; 
    }
    .notification-item {
        transition: background-color 0.2s ease-in-out;
        border-bottom: 1px solid #dee2e6;
    }
    .notification-item:last-child {
        border-bottom: none; 
    }
    .notification-item:hover {
        background-color: #eef2f7; 
    }
    .notification-icon {
        font-size: 1.5rem; 
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
    
    /* Con trỏ khi di chuột vào thông báo */
    .notification-item.clickable {
        cursor: pointer;
    }

    /* Rút gọn message 1 dòng */
    .notification-message-summary {
        font-weight: normal;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .unread .notification-message-summary {
        font-weight: bold;
    }

</style>
@endpush

@section('account_content') {{-- Hoặc @section('content') tùy layout của bạn --}}

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Thông báo</h5>
    </div>
    
    <div class="card-body p-0">
        @if ($notifications->isNotEmpty())
            <div class="list-group list-group-flush">
                @foreach ($notifications as $notification)
                    
                    <div class="list-group-item list-group-item-action py-3 px-4 notification-item clickable {{ $notification->is_read ? '' : 'unread' }}"
                         data-bs-toggle="modal"
                         data-bs-target="#notificationModal-{{ $notification->id }}"
                    >
                        <div class="d-flex align-items-center">
                            
                            {{-- Icon (Giữ nguyên) --}}
                            <div class="notification-icon me-3">
                                @if ($notification->type === 'order_status_updated')
                                    <i class="fas fa-truck text-primary"></i>
                                @elseif ($notification->type === 'review_replied')
                                    <i class="fas fa-comment-dots text-success"></i>
                                @else
                                    <i class="fas fa-bell text-secondary"></i>
                                @endif
                            </div>
                            
                            {{-- Nội dung tóm tắt (message) --}}
                            <div class="ms-3 flex-grow-1" style="min-width: 0;"> 
                                <div class="me-3">

                                    <div class="notification-message-summary {{ $notification->is_read ? '' : 'fw-bold' }}">
                                        {{-- 
                                            Bỏ hàm e() và dùng strip_tags() để hiển thị đúng ký tự ' 
                                            giống như logic trong modal.
                                        --}}
                                        {!! strip_tags(\Illuminate\Support\Str::before($notification->message, "||---REPLY---||")) !!}
                                    </div>
                                    
                                </div>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>

                            {{-- Actions (Giữ nguyên) --}}
                            <div class="ms-auto d-flex">
                                @if (!$notification->is_read)
                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-link" title="Đánh dấu đã đọc" onclick="event.stopPropagation();">
                                            <i class="fas fa-check-circle text-primary fs-5"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link" title="Xóa" onclick="event.stopPropagation(); return confirm('Bạn có chắc muốn xóa thông báo này?')">
                                        <i class="fas fa-trash-alt text-danger fs-5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if ($notifications->hasPages())
                <div class="card-footer bg-white">
                    {{ $notifications->links() }}
                </div>
            @endif
            
        @else
            <div class="text-center py-5">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Không có thông báo nào.</h5>
                <p class="text-muted mb-0">Tất cả thông báo của bạn sẽ xuất hiện ở đây.</p>
            </div>
        @endif
    </div>
</div>


@foreach ($notifications as $notification)
    <div class="modal fade" id="notificationModal-{{ $notification->id }}" tabindex="-1" aria-labelledby="notificationModalLabel-{{ $notification->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel-{{ $notification->id }}">
                        {{-- Tiêu đề (Giữ nguyên) --}}
                        @if ($notification->type === 'review_replied')
                            <i class="fas fa-comment-dots text-success me-2"></i> Phản hồi đánh giá
                        @elseif ($notification->type === 'order_status_updated')
                            <i class="fas fa-truck text-primary me-2"></i> Cập nhật đơn hàng
                        @else
                            <i class="fas fa-bell me-2"></i> Chi tiết thông báo
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @php
                        $separator = "||---REPLY---||";
                        $messageParts = explode($separator, $notification->message, 2);
                        
                        $summary = strip_tags(trim($messageParts[0]));
                        $replyContent = isset($messageParts[1]) ? strip_tags(trim($messageParts[1])) : null;
                    @endphp

                    {{-- 1. Hiển thị tóm tắt (Giữ nguyên) --}}
                    <p class="text-dark">{{ $summary }}</p>

                    {{-- 
                        Xóa khối @elseif gây ra lỗi trùng lặp.
                        Chỉ hiển thị blockquote NẾU CÓ $replyContent
                    --}}
                    @if($replyContent && $notification->type === 'review_replied')
                        <hr>
                        <blockquote class="blockquote bg-light p-3 rounded mt-2 mb-0">
                            <p class="mb-0">{!! nl2br($replyContent) !!}</p>
                        </blockquote>
                    @endif
                    
                    <small class="text-muted d-block mt-3">
                        {{ $notification->created_at->diffForHumans() }} ({{ $notification->created_at->format('H:i d/m/Y') }})
                    </small>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('scripts')
<script>
    // Ngăn modal kích hoạt khi nhấp vào nút (Giữ nguyên)
    document.addEventListener('DOMContentLoaded', function () {
        const actionButtons = document.querySelectorAll('.notification-item form button');
        actionButtons.forEach(button => {
            button.addEventListener('click', function (event) {
                event.stopPropagation();
            });
        });
    });
</script>
@endpush