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
    <div class="table-responsive">
        <table class="table table-striped">
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
                        <div class="d-flex flex-column flex-md-row">

                            <button wire:click="approve({{ $product->id }})" class="bg-green-500 btn-primary px-2 py-1 rounded mb-1 mb-md-0 me-md-1">
                                Duyệt
                            </button>

                            <button wire:click="openRejectModal({{ $product->id }})" class="bg-red-500 btn-danger px-2 py-1 rounded">
                                Từ chối
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $products->links() }}
        <div x-data="{ open: @entangle('reasonModalOpen') }">
        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75">
            <div class="bg-white p-6 rounded shadow-lg w-1/3">
                <h2 class="text-xl mb-4">Lý do từ chối sản phẩm</h2>
                <h5 class="text-lg mb-2">Từ chối đơn bán số ID: {{ $selectedProductId }}</h5>
                <select wire:model="actionType" class="border p-2 rounded mb-4">
                    <option value="reject">Từ chối</option>
                    <option value="hidden">Ẩn sản phẩm</option>
                </select>
                <textarea wire:model="reason" class="w-full border p-2 rounded mb-4" rows="4" placeholder="lý do (tell me why)"></textarea>
                <div class="flex justify-end">
                    <button wire:click="closeRejectModal" class="bg-gray-500 btn-secondary px-4 py-2 rounded mr-2">Hủy</button>
                    <button wire:click="confirmReject" class="bg-red-500 btn-danger px-4 py-2 rounded">Xác nhận</button>
                </div>
</div>

