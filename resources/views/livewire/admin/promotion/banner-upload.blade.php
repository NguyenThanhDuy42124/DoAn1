<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">

            {{-- ✅ Thông báo --}}
            @if (session()->has('message'))
                <div class="alert alert-success" role="alert">
                    {{ session('message') }}
                </div>
            @endif

            {{-- ✅ Form upload (Thiết kế lại dạng Card) --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Quản lý Banner</h5>
                    
                    <form wire:submit.prevent="save">
                        {{-- Input file --}}
                        <div class="mb-3">
                            <input type="file" wire:model="banner" class="form-control" id="bannerUpload">
                            
                            @error('banner')
                                {{-- Hiển thị lỗi --}}
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Nút Lưu (có trạng thái loading) --}}
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save">
                                Lưu banner
                            </span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Đang lưu...
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- ✅ Xem trước ảnh mới --}}
            @if ($banner)
                <div class="mb-4">
                    <h3 class="fw-medium mb-2">Xem trước:</h3>
                    {{-- Dùng class img-fluid của Bootstrap để ảnh responsive --}}
                    <img src="{{ $banner->temporaryUrl() }}" class="img-fluid w-100 rounded shadow border">
                </div>
            @endif

            {{-- ✅ Danh sách banner hiện có --}}
            <div class="mt-4">
                <h3 class="fw-medium mb-3">Danh sách banner hiện có:</h3>

                @if (!empty($banners) && count($banners) > 0)
                    {{-- Dùng Row/Col của Bootstrap để tạo grid --}}
                    {{-- g-4 (gap 4) | col-6 (2 cột mobile) | col-md-4 (3 cột tablet) | col-lg-3 (4 cột desktop) --}}
                    <div class="row g-4">
                        @foreach ($banners as $index => $file)
                            <div class="col-6 col-md-4 col-lg-3">
                                {{-- Dùng Card của Bootstrap --}}
                                <div class="card shadow-sm h-100">
                                    {{-- 
                                      Sử dụng style inline để mô phỏng chính xác class "h-32 object-cover" của Tailwind.
                                      Bootstrap không có class helper cho việc này.
                                    --}}
                                    <img src="{{ asset('storage/' . $file) }}" 
                                         alt="Banner {{ $index + 1 }}" 
                                         class="card-img-top" 
                                         style="height: 128px; object-fit: cover;">
                                         
                                    <div class="card-body text-center d-flex flex-column justify-content-center p-2">
                                        <p class="small fw-medium mb-2">Banner #{{ $index + 1 }}</p>
                                        
                                        {{-- Nút Xóa (có trạng thái loading) --}}
                                        <button wire:click="deleteBanner('{{ $file }}')" 
                                                class="btn btn-danger btn-sm"
                                                wire:loading.attr="disabled" 
                                                wire:target="deleteBanner('{{ $file }}')">
                                            
                                            <span wire:loading.remove wire:target="deleteBanner('{{ $file }}')">
                                                Xóa banner
                                            </span>
                                            <span wire:loading wire:target="deleteBanner('{{ $file }}')">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Đang xóa...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Trạng thái rỗng --}}
                    <p class="text-muted">Chưa có banner nào.</p>
                @endif
            </div>

        </div>
    </div>
</div>