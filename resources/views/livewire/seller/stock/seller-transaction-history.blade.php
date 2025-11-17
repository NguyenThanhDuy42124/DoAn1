<div>
    {{-- 1. Tiêu đề và 2 Nút hành động MỚI --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <h1 class="page-title">Lịch sử Xuất / Nhập kho</h1>
        <div class="btn-list">
            <button class="btn btn-outline-warning" wire:click="openExportModal">
                <i class="bi bi-dash-circle"></i> Tạo Phiếu Xuất
            </button>
            <button class="btn btn-primary" wire:click="openImportModal">
                <i class="bi bi-plus-circle"></i> Tạo Phiếu Nhập
            </button>
        </div>
    </div>

    {{-- Hiển thị thông báo success/error --}}
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif


    {{-- 2. Khối Filter và Bảng Lịch sử (Giữ nguyên) --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Bộ lọc</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="filterProduct" class="form-label">Tên sản phẩm</label>
                    <input type="text" wire:model.live.debounce.300ms="filterProduct" class="form-control" id="filterProduct" placeholder="Nhập tên sản phẩm...">
                </div>
                <div class="col-md-4">
                    <label for="filterType" class="form-label">Loại giao dịch</label>
                    <select wire:model.live="filterType" class="form-select" id="filterType">
                        <option value="">Tất cả</option>
                        <option value="import">Nhập kho (Import)</option>
                        <option value="export">Xuất kho (Export)</option>
                        <option value="sale">Bán hàng (Sale)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Ngày tạo</th>
                        <th>Sản phẩm</th>
                        <th>Loại</th>
                        <th>Số lượng</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                        <tr>
                            <td data-label="Ngày tạo">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                            <td data-label="Sản phẩm">
                                {{ $tx->product->name ?? '[Sản phẩm đã xóa]' }}
                            </td>
                            <td data-label="Loại">
                                @if($tx->transaction_type == 'import')
                                    <span class="badge bg-success-lt">Nhập kho</span>
                                @elseif($tx->transaction_type == 'export')
                                    <span class="badge bg-warning-lt">Xuất kho</span>
                                @else
                                    <span class="badge bg-info-lt">Bán hàng</span>
                                @endif
                            </td>
                            <td data-label="Số lượng">
                                @if($tx->transaction_type == 'import')
                                    <strong class="text-success">+{{ $tx->quantity }}</strong>
                                @else
                                    <strong class="text-danger">-{{ $tx->quantity }}</strong>
                                @endif
                            </td>
                            <td data-label="Ghi chú">{{ Str::limit($tx->notes, 50) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Chưa có giao dịch nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex align-items-center">
            {{ $transactions->links() }}
        </div>
    </div>


    {{-- 3. MODAL NHẬP KHO (Code mới) --}}
    @if($showImportModal)
    <div class="modal modal-blur fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> {{-- Thêm modal-lg cho rộng --}}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo Phiếu Nhập Kho (Hàng Loạt)</h5>
                    <button type="button" class="btn-close" wire:click="closeImportModal"></button>
                </div>
                <div class="modal-body">
                    
                    {{-- Sửa: Dùng vòng lặp @foreach --}}
                    @foreach($importItems as $index => $item)
                        <div class="row g-2 mb-2 align-items-center">
                            {{-- Cột chọn sản phẩm --}}
                            <div class="col-md-6">
                                @if($index == 0) <label class="form-label">Sản phẩm</label> @endif
                                <select wire:model="importItems.{{ $index }}.product_id" 
                                        class="form-select @error('importItems.'.$index.'.product_id') is-invalid @enderror">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    @foreach($importableProducts as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                @error('importItems.'.$index.'.product_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Cột nhập số lượng --}}
                            <div class="col-md-4">
                                @if($index == 0) <label class="form-label">Số lượng</label> @endif
                                <input type="number" 
                                       wire:model="importItems.{{ $index }}.quantity" 
                                       class="form-control @error('importItems.'.$index.'.quantity') is-invalid @enderror" min="1">
                                @error('importItems.'.$index.'.quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Cột nút Xóa --}}
                            <div class="col-md-2 text-end" @if($index == 0) style="padding-top: 28px;" @endif>
                                @if(count($importItems) > 1) {{-- Chỉ cho xóa nếu có nhiều hơn 1 dòng --}}
                                <button class="btn btn-outline-danger btn-icon" 
                                        wire:click.prevent="removeImportItem({{ $index }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- Nút Thêm sản phẩm --}}
                    <div class="mt-3">
                        <button class="btn btn-sm btn-outline-primary" wire:click.prevent="addImportItem">
                            <i class="bi bi-plus"></i> Thêm sản phẩm
                        </button>
                    </div>

                    <hr>
                    
                    {{-- Ghi chú chung (Giữ nguyên) --}}
                    <div class="mb-3">
                        <label class="form-label">Ghi chú chung (Tùy chọn)</label>
                        <textarea wire:model="import_notes" 
                                  class="form-control @error('import_notes') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Nhập từ nhà cung cấp X..."></textarea>
                        @error('import_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" wire:click="closeImportModal">Hủy</button>
                    <button type="button" class="btn btn-primary" wire:click="saveImport">
                        <div wire:loading wire:target="saveImport" class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Lưu Phiếu Nhập
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div> {{-- Lớp mờ nền --}}
    @endif


    {{-- 4. MODAL XUẤT KHO (Code mới) --}}
    @if($showExportModal)
    <div class="modal modal-blur fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tạo Phiếu Xuất Kho (Điều chỉnh)</h5>
                    <button type="button" class="btn-close" wire:click="closeExportModal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Chọn sản phẩm</label>
                        <select wire:model="export_product_id" class="form-select @error('export_product_id') is-invalid @enderror">
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach($importableProducts as $product) {{-- Dùng chung danh sách --}}
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('export_product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Số lượng xuất</label>
                        <input type="number" wire:model="export_quantity" class="form-control @error('export_quantity') is-invalid @enderror" min="1">
                        @error('export_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Lý do xuất kho</label>
                        <textarea wire:model="export_notes" class="form-control @error('export_notes') is-invalid @enderror" rows="3" placeholder="Ví dụ: Hàng lỗi, Hàng hỏng..."></textarea>
                        @error('export_notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" wire:click="closeExportModal">Hủy</button>
                    <button type="button" class="btn btn-warning" wire:click="saveExport">
                        <div wire:loading wire:target="saveExport" class="spinner-border spinner-border-sm me-2" role="status"></div>
                        Lưu Phiếu Xuất
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div> {{-- Lớp mờ nền --}}
    @endif

</div>