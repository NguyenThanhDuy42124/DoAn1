<div>
    <div class="container py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-import mr-2"></i> Nhập Sản Phẩm Excel</h2>
            <a href="{{ route('seller.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        {{-- Thông báo --}}
        @if (session()->has('warning'))
    <div class="alert alert-warning flex justify-between items-center">
        <div>
            {{ session('warning') }}
        </div>
        
        {{-- Nút tải file lỗi --}}
        @if(count($errorRows) > 0)
            <button wire:click="downloadErrorFile" 
                    wire:loading.attr="disabled"
                    class="btn btn-danger btn-sm ml-4 font-bold text-white bg-red-600 hover:bg-red-700 px-4 py-2 rounded">
                <i class="fas fa-file-excel mr-2"></i> Tải File Lỗi & Sửa
            </button>
        @endif
    </div>
@endif

@if (session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

        <div class="card shadow-sm">
            <div class="card-body">
                <form wire:submit.prevent="import">
                    
                    {{-- 1. CHỌN DANH MỤC --}}
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Bước 1: Chọn Danh Mục Sản Phẩm</label>
                        <select wire:model.live="category_id" class="form-control" style="height: 50px;">
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    {{-- PHẦN HIỂN THỊ THÔNG TIN ĐỘNG (Chỉ hiện khi đã chọn danh mục) --}}
                    @if($category_id)
                        <div class="alert alert-info border-info">
                            <h5><i class="fas fa-info-circle"></i> Thông tin cho danh mục này:</h5>
                            <p class="mb-1">Hệ thống sẽ yêu cầu các thuộc tính sau trong file Excel:</p>
                            
                            {{-- Liệt kê các thuộc tính động --}}
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @forelse($previewAttributes as $attr)
                                    <span class="badge badge-primary p-2 mr-2 mb-2" style="font-size: 14px;">
                                        {{ $attr->name }}
                                    </span>
                                @empty
                                    <span class="text-muted">Danh mục này chưa có thuộc tính đặc biệt nào.</span>
                                @endforelse
                            </div>

                            {{-- NÚT TẢI FILE MẪU --}}
                            <button type="button" wire:click="downloadTemplate" class="btn btn-success">
                                <i class="fas fa-download mr-1"></i> Tải File Excel Mẫu (Chuẩn cho danh mục này)
                            </button>
                            <div wire:loading wire:target="downloadTemplate" class="text-success ml-2">
                                <i class="fas fa-spinner fa-spin"></i> Đang tạo file...
                            </div>
                        </div>

                        {{-- 2. UPLOAD FILE --}}
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Bước 2: Chọn file Excel đã điền dữ liệu</label>
                            <input type="file" wire:model="excel_file" class="form-control-file border p-2 w-100">
                            @error('excel_file') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        {{-- 3. UPLOAD ẢNH --}}
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Bước 3: Chọn tất cả ảnh liên quan (Nếu có)</label>
                            <input type="file" wire:model="images" multiple class="form-control-file border p-2 w-100">
                            <small class="text-muted">Tên file ảnh phải khớp với tên bạn điền trong cột <code>image_1</code>, <code>image_2</code>... của Excel.</small>
                            @error('images.*') <span class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>

                        <hr>

                        {{-- NÚT IMPORT --}}
                        <button type="submit" class="btn btn-primary btn-lg w-100" wire:loading.attr="disabled" wire:target="import">
                            <span wire:loading.remove wire:target="import">
                                <i class="fas fa-upload mr-2"></i> TIẾN HÀNH NHẬP DỮ LIỆU
                            </span>
                            <span wire:loading wire:target="import">
                                <i class="fas fa-spinner fa-spin"></i> Đang xử lý, vui lòng chờ...
                            </span>
                        </button>
                    @endif

                </form>
            </div>
        </div>
    </div>
</div>