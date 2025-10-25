@extends('layouts.account') {{-- Hoặc layouts.app tùy bạn --}}

@section('title', 'Lịch sử đơn hàng - TechStore')

@section('account_content') {{-- Hoặc 'content' nếu dùng layouts.app --}}
    
    {{-- Component Livewire sẽ được nạp vào đây --}}
    @livewire('buyer-order-history')

@endsection