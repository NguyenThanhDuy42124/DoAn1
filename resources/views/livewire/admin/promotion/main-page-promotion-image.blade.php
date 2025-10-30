<div class="max-w-5xl mx-auto mt-10 p-6 bg-white rounded-xl shadow-md">
    <h1 class="text-2xl font-bold mb-4 text-center">quản lý Ảnh ở trang chủ</h1>

    {{-- Tabs --}}
    <div class="flex space-x-4 border-b mb-4">
        <button wire:click="setTab('banner')"
            class="pb-2 {{ $tab === 'banner' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500' }}">
            Banner
        </button>

        <button wire:click="setTab('other')"
            class="pb-2 {{ $tab === 'other' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500' }}">
            Chức năng khác
        </button>
    </div>

    {{-- Nội dung của từng tab --}}
    <div>
        @if ($tab === 'banner')
            @livewire('admin.promotion.banner-upload')
        @elseif ($tab === 'other')
            <div class="p-4 text-gray-500 text-center">
                (Trang này bạn có thể thêm component khác sau)
            </div>
        @endif
    </div>
</div>
