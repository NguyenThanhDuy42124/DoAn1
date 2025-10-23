<div class="p-4 max-w-5xl mx-auto">
    {{-- Tabs --}}
    <div class="flex border-b mb-4 text-sm font-medium">
        <button wire:click="switchTab('hidden')"
                class="btn-primary rounded px-4 py-2 border-b-2 {{ $tab === 'hidden' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600' }}">
            Ẩn
        </button>
        <button wire:click="switchTab('rejected')"
                class="btn-danger rounded px-4 py-2 border-b-2 {{ $tab === 'rejected' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-600' }}">
            Từ chối
        </button>
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
    <div class="bg-white rounded border overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-3 py-2 text-left">Sản phẩm</th>
                    <th class="px-3 py-2 text-left">Seller</th>
                    <th class="px-3 py-2 text-left">Cập nhật</th>
                    <th class="px-3 py-2 text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr class="border-b hover:bg-gray-50 cursor-pointer" wire:click="toggleExpand({{ $product->id }})">
                        <td class="px-3 py-2">
                            <div class="font-medium">{{ Str::limit($product->name, 35) }}</div>
                            <div class="text-xs text-gray-500">ID: {{ $product->id }}</div>
                        </td>
                        <td class="px-3 py-2 text-gray-600">
                            {{ $product->seller?->name ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-500">
                            {{ $product->updated_at->format('d/m/Y') }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button wire:click.stop="restore({{ $product->id }})"
                                    class="text-green-600 hover:text-green-800 text-xs font-medium">
                                Khôi phục
                            </button>
                        </td>
                    </tr>

                    @if($selectedProduct && $selectedProduct->id === $product->id)
                        <tr>
                            <td colspan="4" class="p-3 bg-gray-50 text-xs">
                                <div><strong>Lý do:</strong>
                                    <span class="text-red-600">
                                        {{ $selectedProduct->notifications->first()?->message ?? 'Không có lý do' }}
                                    </span>
                                </div>
                                <div class="mt-1">
                                    <strong>Lịch sử:</strong>
                                    @foreach(\App\Models\ModerationLog::where('product_id', $product->id)->latest()->take(2)->get() as $log)
                                        <div class="text-gray-600">
                                            {{ $log->admin->name }} → {{ $log->action }}
                                            <span class="text-gray-400">({{ $log->created_at->diffForHumans() }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-gray-500">
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