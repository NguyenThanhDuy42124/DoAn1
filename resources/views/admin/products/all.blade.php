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
                <button wire:click="export" class="btn btn-primary btn-sm me-2">
                   Export Excel
                </button>

                <button wire:click="$set('search', '')" class="btn btn-danger btn-sm">
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
        <div class="table-responsive">
        <table class="table table-striped">
                <thead>
                    <tr>
                        <th wire:click="sortBy('name')" class="border px-4 py-2" style="cursor: pointer;">
                            Tên sản phẩm
                            @if($sortField === 'name') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                        </th>
                        <th wire:click="sortBy('price')" class="border px-4 py-2" style="cursor: pointer;">
                            Giá
                            @if($sortField === 'price') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                        </th>
                        <th class="border px-4 py-2">
                            Danh mục
                        </th>
                        <th class="border px-4 py-2">
                            Seller
                        </th>
                        <th wire:click="sortBy('status')" class="border px-4 py-2" style="cursor: pointer;">
                            Trạng thái
                            @if($sortField === 'status') @if($sortDirection === 'asc') &uarr; @else &darr; @endif @endif
                        </th>
                        <th class="border px-4 py-2">
                            Hành động
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="border px-4 py-2">
                                {{ Str::limit($product->name, 50) }}
                            </td>
                            <td class="border px-4 py-2">
                                {{ number_format($product->price) }}đ
                            </td>
                            <td class="border px-4 py-2">
                                {{ $product->category?->name ?? '-' }}
                            </td>
                            <td class="border px-4 py-2">
                                {{ $product->seller?->name ?? '-' }}
                            </td>
                            <td class="border px-4 py-2">
                                <span>
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                            <td class="border px-4 py-2">
                               <div class="d-flex flex-column flex-md-row">

                            <button wire:click="openRejectModal({{ $product->id }})" class="btn btn-danger btn-sm mr-2">
                                sửa trạng thái
                            </button>

                               <button wire:click="confirmDelete({{ $product->id }})"
                                    class="btn btn-danger btn-sm ">
                                          Xóa
                               </button>
                              </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="border px-4 py-2" style="text-align: center;">Không có sản phẩm nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 15px;">
            {{ $products->links() }}

        </div>

    </div>
        {{-- Modal cho reason (Fix lỗi căn giữa) --}}
    <div x-data="{ open: @entangle('reasonModalOpen') }">
        <div x-show="open"
             style="position: fixed; top: 0; left: 0; width: 100%; height: 100%;
             background-color: rgba(0, 0, 0, 0.6); z-index: 1050;"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="card shadow-lg col-md-4"
                 style="position: absolute; top: 50%; left: 50%;
                 transform: translate(-50%, -50%);">

                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Lý do từ chối sản phẩm</h5>
                    <h6 class="card-subtitle mb-3 text-muted">
                        Từ chối đơn bán số ID: <strong>{{ $selectedProductId }}</strong>
                    </h6>

                    <div class="mb-3">
                        <label class="form-label">Hành động:</label>
                        <select wire:model="actionType" class="form-select">
                            <option value="reject">Từ chối</option>
                            <option value="hidden">Ẩn sản phẩm</option>
                            <option value="approve">Duyệt</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lý do (tell me why):</label>
                        <textarea wire:model="reason" class="form-control" rows="4" placeholder="Lý do..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button wire:click="closeRejectModal" class="btn btn-secondary me-2">Hủy</button>
                        <button wire:click="confirmReject" class="btn btn-danger">Xác nhận</button>
                    </div>
                </div>
            </div>
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
