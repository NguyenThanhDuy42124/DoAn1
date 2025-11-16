{{-- resources/views/pages/static-page.blade.php --}}

@extends('layouts.app')

{{-- Tiêu đề trang (trên tab trình duyệt) sẽ được truyền từ Controller --}}
@section('title', $title ?? 'Thông tin')

@section('content')
<div class="container my-5">
    <div class="row">
        {{-- Giới hạn chiều rộng nội dung để dễ đọc --}}
        <div class="col-lg-10 col-xl-8 mx-auto">
            
            {{-- Tiêu đề chính của trang --}}
            <h1 class="mb-4">{{ $title ?? 'Thông tin' }}</h1>
            
            {{-- Vùng hiển thị nội dung động --}}
            <div class="static-content">
                {{-- 
                  QUAN TRỌNG: Sử dụng {!! $content !!}
                  Điều này cho phép bạn truyền mã HTML (như <p>, <ul>, <h3>)
                  từ Controller vào view mà không bị lỗi.
                --}}
                {!! $content ?? 'Nội dung đang được cập nhật...' !!}
            </div>

        </div>
    </div>
</div>
@endsection

{{-- (Tùy chọn) Thêm CSS nếu bạn muốn style riêng cho nội dung --}}
@push('styles')
<style>
    .static-content h3 {
        margin-top: 2.5rem;
        margin-bottom: 1rem;
    }
    .static-content ul, .static-content ol {
        padding-left: 1.5rem;
    }
    .static-content li {
        margin-bottom: 0.5rem;
    }
</style>
@endpush