<div class="p-4 max-w-5xl mx-auto">
    {{-- Tabs --}}
    {{-- Tabs (dạng select thay vì button) --}}
<div class="mb-4">
    <select
        wire:model.live="tab"
        class="border rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
    >
        <option value="hidden">Ẩn</option>
        <option value="rejected">Từ chối</option>
    </select>
</div>

    {{-- Search + Filter Lý do --}}
    <div class="mb-4 flex gap-2 flex-wrap">
        <input type="text" wire:model.live.debounce.500ms="search"
               class="flex-1 min-w-0 border rounded px-3 py-1 text-sm" placeholder="Tìm tên, seller...">

        <select wire:model.live="reason_filter" class="border rounded px-3 py-1 text-sm">
            <option value="">Tất cả lý do</option>
            @foreach($reasonOptions as $reason)
                <option>{{ $reason }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
     <div class="table-responsive">
        <table class="table table-striped ">

    <thead class="table-light">
        <tr class="border px-4 py-2">
            <th class="text-left border px-4 py-2">Sản phẩm</th>
            <th class="text-left border px-4 py-2">Seller</th>
            <th class="text-left border px-4 py-2">Cập nhật</th>
            <th class="text-center border px-4 py-2">Hành động</th>
        </tr>
    </thead>
    <tbody class="border px-4 py-2">
        @forelse($products as $product)

            <tr wire:click="toggleExpand({{ $product->id }})" style="cursor: pointer;">

                <td class="border px-4 py-2">
                    <div class="fw-medium">{{ Str::limit($product->name, 35) }}</div>
                    <div class="small text-muted">ID: {{ $product->id }}</div>
                </td>

                <td class="text-body-secondary border px-4 py-2">
                    {{ $product->seller?->name ?? '—' }}
                </td>

                <td class="small text-muted border px-4 py-2">
                    {{ $product->updated_at->format('d/m/Y') }}
                </td>

                <td class="text-center border px-4 py-2">
                    <button wire:click.stop="restore({{ $product->id }})"
                            class="btn btn-link text-success text-decoration-none p-0 small fw-medium">
                        Khôi phục
                    </button>
                </td>
            </tr>

            {{-- Hàng chi tiết (khi bấm vào) --}}
            @if($selectedProduct && $selectedProduct->id === $product->id)
                <tr>
                    <td colspan="4" class="p-3 bg-light small">
                        <div>
                            <strong>Lý do:</strong>
                            <span class="text-danger">
                                {{ $selectedProduct->notifications->first()?->message ?? 'Không có lý do' }}
                            </span>
                        </div>
                        <div class="mt-1">
                            <strong>Lịch sử:</strong>
                            @foreach(\App\Models\ModerationLog::where('product_id', $product->id)->latest()->take(2)->get() as $log)
                                <div class="text-body-secondary">
                                    {{ $log->admin->name }} → {{ $log->action }}
                                    <span class="text-muted">({{ $log->created_at->diffForHumans() }})</span>
                                </div>
                            @endforeach
                        </div>
                    </td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="4" class="text-center p-5 text-muted">
                    Không có sản phẩm nào {{ $tab === 'hidden' ? 'bị ẩn' : 'bị từ chối' }}.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

        <div class="p-2 text-center">
            {{ $products->links() }}
        </div>
    </div>
</div>
