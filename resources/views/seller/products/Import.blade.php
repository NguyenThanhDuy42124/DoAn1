<div>
    <div class="container py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-excel mr-2"></i> Quản Lý Sản Phẩm Bằng Excel</h2>
            <a href="{{ route('seller.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        {{-- Thông báo --}}
        @if (session()->has('warning'))
            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                <div>{{ session('warning') }}</div>
                @if(count($errorRows) > 0)
                    <button wire:click="downloadErrorFile" wire:loading.attr="disabled" class="btn btn-danger btn-sm font-weight-bold">
                        <i class="fas fa-file-excel mr-2"></i> Tải File Lỗi & Sửa
                    </button>
                @endif
            </div>
        @endif

        @if (session()->has('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Tabs Navigation --}}
        <ul class="nav nav-tabs mb-0" id="excelTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'new' ? 'active font-weight-bold' : '' }}" 
                   wire:click.prevent="setTab('new')" href="#" style="cursor: pointer;">
                   <i class="fas fa-plus-circle text-success"></i> Nhập Sản Phẩm Mới
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $activeTab == 'update' ? 'active font-weight-bold' : '' }}" 
                   wire:click.prevent="setTab('update')" href="#" style="cursor: pointer;">
                   <i class="fas fa-edit text-primary"></i> Cập Nhật Hàng Loạt
                </a>
            </li>
        </ul>

        <div class="card shadow-sm border-top-0" style="border-top-left-radius: 0; border-top-right-radius: 0;">
            <div class="card-body">
                <form wire:submit.prevent="submit">
                    
                    {{-- 1. CHỌN DANH MỤC --}}
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Bước 1: Chọn Danh Mục {{ $activeTab == 'new' ? 'cần nhập' : 'cần sửa' }}</label>
                        <select wire:model.live="category_id" class="form-control" style="height: 50px;">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- PHẦN HIỂN THỊ THÔNG TIN ĐỘNG --}}
                    @if($category_id)
                        <div class="alert alert-info border-info">
                            <h5><i class="fas fa-info-circle"></i> Thông tin danh mục:</h5>
                            
                            {{-- *** ĐOẠN NÀY LÀ ĐOẠN TAO VỪA TRẢ LẠI CHO MÀY NÈ *** --}}
                            <div class="mb-3">
                                <p class="mb-2 text-dark font-weight-bold">Các thuộc tính sẽ có trong file Excel:</p>
                                <div class="d-flex flex-wrap">
                                    @forelse($previewAttributes as $attr)
                                        <span class="badge badge-light border border-info text-info p-2 mr-2 mb-2" style="font-size: 14px;">
                                            {{ $attr->name }}
                                            @if($attr->unit) <small class="text-muted">({{ $attr->unit }})</small> @endif
                                        </span>
                                    @empty
                                        <span class="text-muted font-italic">Danh mục này chỉ có thông tin cơ bản (Tên, Giá, Mô tả...), không có thuộc tính đặc biệt.</span>
                                    @endforelse
                                </div>
                            </div>
                            <hr class="border-info">
                            {{-- *** HẾT ĐOẠN THÊM VÀO *** --}}

                            {{-- LOGIC NÚT TẢI KHÁC NHAU THEO TAB --}}
                            @if($activeTab == 'new')
                                <p>Tải file mẫu trắng để điền thông tin sản phẩm mới.</p>
                                <button type="button" wire:click="downloadTemplate" class="btn btn-success">
                                    <i class="fas fa-download mr-1"></i> Tải File Mẫu (Nhập Mới)
                                </button>
                            @else
                                <p>Tải danh sách sản phẩm hiện có về máy để chỉnh sửa.</p>
                                <button type="button" wire:click="downloadExportData" class="btn btn-primary">
                                    <i class="fas fa-file-export mr-1"></i> Xuất Dữ Liệu (Để Sửa)
                                </button>
                            @endif

                            <div wire:loading wire:target="downloadTemplate, downloadExportData" class="text-primary ml-2">
                                <i class="fas fa-spinner fa-spin"></i> Đang xử lý file...
                            </div>
                        </div>

                        {{-- 2. UPLOAD FILE --}}
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Bước 2: Upload file Excel {{ $activeTab == 'new' ? 'nhập mới' : 'đã chỉnh sửa' }}</label>
                            <input type="file" wire:model="excel_file" class="form-control-file border p-2 w-100">
                            @error('excel_file') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- 3. UPLOAD ẢNH --}}
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Bước 3: Upload ảnh (Nếu có)</label>
                            <input type="file" wire:model="images" multiple class="form-control-file border p-2 w-100">
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-exclamation-triangle"></i> Lưu ý: Tên file ảnh phải khớp chính xác với cột <code>image_...</code> trong Excel.
                            </small>
                            @error('images.*') <span class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>

                        <hr>

                        {{-- NÚT IMPORT --}}
                        <button type="submit" class="btn btn-{{ $activeTab == 'new' ? 'success' : 'primary' }} btn-lg w-100" 
                                wire:loading.attr="disabled" wire:target="submit">
                            <span wire:loading.remove wire:target="submit">
                                @if($activeTab == 'new')
                                    <i class="fas fa-plus-circle mr-2"></i> THÊM MỚI SẢN PHẨM
                                @else
                                    <i class="fas fa-save mr-2"></i> CẬP NHẬT DỮ LIỆU
                                @endif
                            </span>
                            <span wire:loading wire:target="submit">
                                <i class="fas fa-spinner fa-spin"></i> Đang xử lý...
                            </span>
                        </button>
                    @endif

                </form>
            </div>
        </div>
    </div>
</div>