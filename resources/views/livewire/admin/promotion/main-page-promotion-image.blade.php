{{-- 
  Sử dụng container-lg (tương đương max-w-5xl), mt-5 (thay cho mt-10), 
  p-4 (thay cho p-6) và các class shadow, rounded của Bootstrap
--}}
<div class="container-lg mt-5 p-4 bg-white rounded shadow-sm">
    
    {{-- Sử dụng class h3 (tương đương text-2xl) và fw-bold (font-bold) --}}
    <h1 class="h3 fw-bold mb-4 text-center">Banner & Promotion</h1>

    {{-- Tabs (Dùng cấu trúc nav-tabs của Bootstrap) --}}
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            {{-- 
              Dùng <button> với class "nav-link" để kích hoạt wire:click.
              Thêm class "active" của Bootstrap khi tab được chọn.
            --}}
            <button class="nav-link {{ $tab === 'banner' ? 'active' : '' }}" wire:click="setTab('banner')">
                Banner
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $tab === 'other' ? 'active' : '' }}" wire:click="setTab('other')">
                Promotion?
            </button>
        </li>
    </ul>

    {{-- Nội dung của từng tab --}}
    <div>
        @if ($tab === 'banner')
            {{-- Component Livewire được giữ nguyên --}}
            @livewire('admin.promotion.banner-upload')
            
        @elseif ($tab === 'other')
            {{-- Dùng class "text-muted" của Bootstrap (thay cho text-gray-500) --}}
            <div class="p-4 text-muted text-center">
                (Trang này bạn có thể thêm component khác sau)
            </div>
        @endif
    </div>
</div>