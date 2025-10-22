<div>
    {{-- Header: Search + Filters + Export --}}
    <div>
        <fieldset>
            <legend>Bộ lọc và Tìm kiếm</legend>
            <div>
                <label for="search">Tìm kiếm:</label>
                <input type="text" id="search" wire:model.live.debounce.500ms="search"
                    placeholder="Tìm tên, mô tả, seller...">
            </div>
            
            <div style="margin-top: 10px;">
                <label for="status">Trạng thái:</label>
                <select id="status" wire:model.live="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending">Chờ duyệt</option>
                    <option value="approved">Đã duyệt</option>
                    <option value="rejected">Bị từ chối</option>
                    <option value="hidden">Ẩn</option>
                </select>
            </div>

            <div style="margin-top: 10px;">
                <label for="category">Danh mục:</label>
                <select id="category" wire:model.live="category_id">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-top: 10px;">
                <label for="seller">Seller:</label>
                <select id="seller" wire:model.live="seller_id">
                    <option value="">Tất cả seller</option>
                    @foreach($users as $seller)
                        <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-top: 10px;">
                <label for="price_min">Giá từ:</label>
                <input type="number" id="price_min" wire:model.live="price_min" placeholder="Giá từ" style="width: 80px;">
                <span>-</span>
                <label for="price_max">Giá đến:</label>
                <input type="number" id="price_max" wire:model.live="price_max" placeholder="Giá đến" style="width: 80px;">
            </div>

            <div style="margin-top: 15px;">
                <button wire:click="export">
                    Export Excel
                </button>
                <button wire:click="$set('search', '')">
                    Xóa bộ lọc
                </button>
            </div>
        </fieldset>
    </div>

    <hr style="margin: 20px 0;">

    {{-- Quick Stats --}}
    <div>
        <h3>Thống kê nhanh</h3>
        <div style="display: flex; gap: 20px;">
             <div>
                <h2>{{ $stats['total'] }}</h2>
                <p>Tổng sản phẩm</p>
            </div>
            <div>
                <h2>{{ $stats['approved_percent'] }}%</h2>
                <p>Đã duyệt</p>
            </div>
            <div>
                <h2>{{ $stats['pending'] }}</h2>
                <p>Chờ duyệt</p>
            </div>
            <div>
                <h2>{{ $stats['rejected'] }}</h2>
                <p>Bị từ chối</p>
            </div>
        </div>
    </div>

    <hr style="margin: 20px 0;">

    {{-- Chart --}}
    <div>
        <h3>Top danh mục</h3>
        <div wire:ignore>
            <canvas id="categoryChart" height="100"></canvas>
        </div>
    </div>

    <hr style="margin: 20px 0;">

    {{-- Table --}}
    <div>
        <div>
            <table border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th wire:click="sortBy('name')" style="cursor: pointer;">
                            Tên sản phẩm
                            @if($sortField === 'name') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                        </th>
                        <th wire:click="sortBy('price')" style="cursor: pointer;">
                            Giá
                            @if($sortField === 'price') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                        </th>
                        <th>
                            Danh mục
                        </th>
                        <th>
                            Seller
                        </th>
                        <th wire:click="sortBy('status')" style="cursor: pointer;">
                            Trạng thái
                            @if($sortField === 'status') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                        </th>
                        <th>
                            Hành động
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                {{ Str::limit($product->name, 50) }}
                            </td>
                            <td>
                                {{ number_format($product->price) }}đ
                            </td>
                            <td>
                                {{ $product->category?->name ?? '-' }}
                            </td>
                            <td>
                                {{ $product->seller?->name ?? '-' }}
                            </td>
                            <td>
                                <span>
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', $product) }}">Sửa</a>
                                <button wire:click="confirmDelete({{ $product->id }})">Xóa</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">Không có sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 15px;">
            {{ $products->links() }}
        </div>
    </div>

    {{-- Chart.js Script --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:load', () => {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            const data = @json(json_decode($chartData, true));

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(d => d.label),
                    datasets: [{
                        data: data.map(d => d.value),
                        backgroundColor: data.map(d => d.color),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'right' },
                        tooltip: { callbacks: { label: ctx => `${ctx.label}: ${ctx.raw} sản phẩm` } }
                    }
                }
            });
        });
    </script>
    @endpush
</div>