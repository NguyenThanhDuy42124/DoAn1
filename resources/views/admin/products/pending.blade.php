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
       {{-- Modal cho reason (Fix lỗi căn giữa) --}}
<div x-data="{ open: @entangle('reasonModalOpen') }">
    
    <div x-show="open" 
         style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); z-index: 1050;"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="card shadow-lg col-md-4" 
             style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            
            <div class="card-body p-4">
                <h5 class="card-title mb-3">Lý do từ chối sản phẩm</h5>
                <h6 class="card-subtitle mb-3 text-muted">Từ chối đơn bán số ID: <strong>{{ $selectedProductId }}</strong></h6>
                
                <div class="mb-3">
                    <label class="form-label">Hành động:</label>
                    <select wire:model="actionType" class="form-select">
                        <option value="reject">Từ chối</option>
                        <option value="hidden">Ẩn sản phẩm</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lý do (tell me why):</label>
                    <textarea wire:model="reason" class="form-control" rows="4" placeholder="lý do..."></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button wire:click="closeRejectModal" class="btn btn-secondary me-2">Hủy</button>
                    <button wire:click="confirmReject" class="btn btn-danger">Xác nhận</button>
                </div>
            </div>

        </div> </div> </div> 


