<div>
    <div class="mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Tìm tên sản phẩm, mô tả, seller..." class="border p-2 w-full rounded">
    </div>

    <form wire:submit.prevent="bulkApprove" class="mb-4">
        <button type="submit" class="bg-green-500 btn-primary px-4 py-2 rounded">Duyệt hàng loạt</button>
        <button type="button" wire:click="bulkReject" class="bg-red-500 btn-danger px-4 py-2 rounded ml-2">Từ chối hàng loạt</button>
    </form>

    {{-- Modal cho reason (dùng x-data Alpine) --}}
    <div x-data="{ open: false }" class="mb-4">
        <input type="text" wire:model="reason" placeholder="Lý do từ chối (cho bulk)" class="border p-2 w-full rounded" x-show="open">
    </div>

    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="border px-4 py-2"><input type="checkbox" wire:model.live="selectAll"></th>
                <th class="border px-4 py-2">ID</th>
                <th class="border px-4 py-2">Tên</th>
                <th class="border px-4 py-2">Giá</th>
                <th class="border px-4 py-2">Seller</th>
                <th class="border px-4 py-2">Danh mục</th>
                <th class="border px-4 py-2">Ngày đăng</th>
                <th class="border px-4 py-2">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td class="border px-4 py-2"><input type="checkbox" wire:model.live="selected" value="{{ $product->id }}"></td>
                    <td class="border px-4 py-2">{{ $product->id }}</td>
                    <td class="border px-4 py-2">
                        {{ $product->name }}
                        {{-- Preview modal: Thêm Alpine hoặc Livewire modal để show desc/images --}}
                    </td>
                    <td class="border px-4 py-2">{{ number_format($product->price) }} VND</td>
                    <td class="border px-4 py-2">{{ $product->seller->name }}</td>
                    <td class="border px-4 py-2">{{ $product->category->name }}</td>
                    <td class="border px-4 py-2">{{ $product->created_at->format('d/m/Y') }}</td>
                    <td class="border px-4 py-2">
                        <button wire:click="approve({{ $product->id }})" class="bg-green-500 btn-primary px-2 py-1 rounded">Duyệt</button>
                        <button wire:click="reject({{ $product->id }})" class="bg-red-500 btn-danger px-2 py-1 rounded ml-2">Từ chối</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $products->links() }}
</div>