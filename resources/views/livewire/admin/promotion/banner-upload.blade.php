<div class="space-y-4">
    {{-- ✅ Thông báo --}}
    @if (session()->has('message'))
        <div class="p-3 bg-green-100 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- ✅ Form upload --}}
    <form wire:submit.prevent="save" class="space-y-3">
        <input type="file" wire:model="banner" class="w-full border rounded p-2">

        @error('banner')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Lưu banner
        </button>
    </form>

    {{-- ✅ Xem trước ảnh mới --}}
    @if ($banner)
        <div class="mt-4">
            <h3 class="font-medium mb-2">Xem trước:</h3>
            <img src="{{ $banner->temporaryUrl() }}" class="w-full rounded shadow">
        </div>
    @endif

    {{-- ✅ Danh sách banner hiện có --}}
    <div class="mt-6">
        <h3 class="font-medium mb-3">Danh sách banner hiện có:</h3>

        @if (!empty($banners) && count($banners) > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($banners as $index => $file)
                    <div class="border rounded-lg p-2 shadow-sm bg-white">
                        <p class="text-sm font-medium text-center mb-2">Banner #{{ $index + 1 }}</p>
                        <img src="{{ asset('storage/' . $file) }}" alt="Banner {{ $index + 1 }}" class="rounded w-full h-32 object-cover">
                        <div class="mt-2 text-center">
                            <button wire:click="deleteBanner('{{ $file }}')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">
                                Xóa banner
                            </button>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-sm">Chưa có banner nào.</p>
        @endif
    </div>
</div>
