@extends('layouts.AdminDashBoard')
@section('content')
<div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-4">Quản lý sản phẩm</h1>
        
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'pending' ? 'active' : '' }}" href="{{ route('admin.products.index', ['tab' => 'pending']) }}">Chờ duyệt {{-- Thêm count sau --}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'all' ? 'active' : '' }}" href="{{ route('admin.products.index', ['tab' => 'all']) }}">Tất cả</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'hidden' ? 'active' : '' }}" href="{{ route('admin.products.index', ['tab' => 'hidden-rejected']) }}">Bị ẩn & Từ chối</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') == 'categories' ? 'active' : '' }}" href="{{ route('admin.products.index', ['tab' => 'categories']) }}">Danh mục</a>
            </li>
        </ul>

        <div class="tab-content">
            @if(request('tab') == 'pending' || !request('tab')) {{-- Default pending --}}
                @livewire('admin.products.pending')
            @elseif(request('tab') == 'all')
                @livewire('admin.products.all')
            @elseif(request('tab') == 'hidden-rejected')
                @livewire('admin.products.hidden-rejected')
            @elseif(request('tab') == 'categories')
                @livewire('admin.categories.manager')
            @endif
        </div>
    </div>
@endsection