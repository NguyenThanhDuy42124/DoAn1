@extends('layouts.SellerDashBoard')
@section('content')

<div>
    <div class="container">
        {{-- Tiêu đề trang --}}
        <div class="page-header mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title">
                    @if($productId)
                        <i class="fas fa-edit mr-2"></i> Chỉnh sửa Sản phẩm
                    @else
                        <i class="fas fa-plus-circle mr-2"></i> Tạo Sản phẩm Mới
                    @endif
                </h2>
                <a href="{{ route('seller.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </a>
            </div>
        </div>

        {{-- Hiển thị lỗi validation chung --}}
        @if ($errors->any())
          <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
          </div>
        @endif

        {{-- Form chính --}}
        <div class="form-container card shadow-sm my-4">
            <div class="card-body">
                {{-- Dùng wire:submit thay vì action --}}
                <form wire:submit.prevent="save">
                    {{-- Không cần CSRF với Livewire --}}
                    {{-- Không cần seller_id vì component tự lấy Auth::id() --}}

                    {{-- === PHẦN THÔNG TIN CƠ BẢN === --}}
                    <h5 class="mb-3 border-bottom pb-2">Thông tin cơ bản</h5>

                    {{-- Danh mục --}}
                    <div class="mb-3 row">
                        <label for="category_id" class="col-sm-3 col-form-label required">Danh mục</label>
                        <div class="col-sm-9">
                            {{-- wire:model.live để load thuộc tính ngay khi chọn --}}
                            <select wire:model.live="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($allCategories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Tên sản phẩm --}}
                    <div class="mb-3 row">
                        <label for="name" class="col-sm-3 col-form-label required">Tên sản phẩm</label>
                        <div class="col-sm-9">
                            <input type="text" wire:model.defer="name" id="name" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Giá --}}
                    <div class="mb-3 row">
                        <label for="price" class="col-sm-3 col-form-label required">Giá (VNĐ)</label>
                        <div class="col-sm-9">
                            <input type="number" step="1000" wire:model.defer="price" id="price" class="form-control @error('price') is-invalid @enderror" required>
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Thương hiệu --}}
                    <div class="mb-3 row">
                        <label for="brand_id" class="col-sm-3 col-form-label">Thương hiệu</label>
                        <div class="col-sm-9">
                            <select wire:model.defer="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                                <option value="">-- Chọn thương hiệu (Nếu có) --</option>
                                @foreach($allBrands as $brand)
                                    <option value="{{ $brand->id }}">
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Tồn kho --}}
                    <div class="mb-3 row">
                        <label for="stock" class="col-sm-3 col-form-label required">Tồn kho</label>
                        <div class="col-sm-9">
                            <input type="number" wire:model.defer="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" required>
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Mô tả --}}
                    <div class="mb-3 row">
                        <label for="description" class="col-sm-3 col-form-label">Mô tả</label>
                        <div class="col-sm-9">
                            <textarea wire:model.defer="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="5"></textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- === PHẦN THUỘC TÍNH ĐỘNG === --}}
                    {{-- Chỉ hiện khi đã chọn Danh mục và có thuộc tính --}}
                    @if($categoryAttributes->isNotEmpty())
                    <h5 class="mt-4 mb-3 border-bottom pb-2">Thuộc tính chi tiết</h5>

                    @foreach($categoryAttributes as $attribute)
                        <div class="mb-3 row" wire:key="attribute-{{ $attribute->id }}">
                            <label for="attribute-{{ $attribute->id }}" class="col-sm-3 col-form-label">{{ $attribute->name }}</label>
                            <div class="col-sm-9">
                                {{-- *** ĐỔI TÊN wire:model VÀ @error *** --}}
                                @if($attribute->type == 'text')
                                    <input type="text"
                                           wire:model.defer="attributeValues.{{ $attribute->id }}" {{-- <-- Đổi --}}
                                           id="attribute-{{ $attribute->id }}"
                                           class="form-control @error('attributeValues.'.$attribute->id) is-invalid @enderror"> {{-- <-- Đổi --}}

                                @elseif($attribute->type == 'number')
                                    <div class="input-group">
                                        <input type="number" step="any"
                                               wire:model.defer="attributeValues.{{ $attribute->id }}" {{-- <-- Đổi --}}
                                               id="attribute-{{ $attribute->id }}"
                                               class="form-control @error('attributeValues.'.$attribute->id) is-invalid @enderror"> {{-- <-- Đổi --}}
                                        @if($attribute->unit)
                                            <span class="input-group-text">{{ $attribute->unit }}</span>
                                        @endif
                                    </div>

                                @elseif($attribute->type == 'select')
                                    <select wire:model.defer="attributeValues.{{ $attribute->id }}" {{-- <-- Đổi --}}
                                            id="attribute-{{ $attribute->id }}"
                                            class="form-select @error('attributeValues.'.$attribute->id) is-invalid @enderror"> {{-- <-- Đổi --}}
                                        <option value="">-- Chọn {{ $attribute->name }} --</option>
                                        @foreach($attribute->options as $option)
                                            <option value="{{ $option->value }}">{{ $option->value }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('attributeValues.'.$attribute->id) <div class="invalid-feedback">{{ $message }}</div> @enderror {{-- <-- Đổi --}}
                            </div>
                        </div>
                    @endforeach
                @endif

                    {{-- === PHẦN HÌNH ẢNH === --}}
                    <h5 class="mt-4 mb-3 border-bottom pb-2">Hình ảnh sản phẩm</h5>

                    {{-- Hiển thị ảnh CŨ (nếu là edit) --}}
                    @if($productId && !empty($existingImages))
                        <div class="mb-3">
                            <label class="form-label">Ảnh hiện tại:</label>
                            <div class="row g-2">
                                @foreach($existingImages as $img)
                                    <div class="col-auto existing-image-thumb" wire:key="existing-img-{{ $img['id'] }}">
                                        <img src="{{ asset('storage/' . $img['image_path']) }}" alt="Ảnh sản phẩm" class="img-thumbnail" width="100">
                                        <button type="button" class="btn btn-danger btn-sm remove-image-btn"
                                                wire:click.prevent="removeExistingImage({{ $img['id'] }})"
                                                title="Xóa ảnh này">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">Bấm vào dấu X để xóa ảnh hiện tại.</small>
                        </div>
                    @endif

                    {{-- Upload ảnh MỚI --}}
                    <div class="mb-3 row">
                        <label for="images" class="col-sm-3 col-form-label">Thêm ảnh mới</label>
                        <div class="col-sm-9">
                            <input type="file" wire:model="images" id="images" class="form-control @error('images.*') is-invalid @enderror" multiple>
                            @error('images.*') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            {{-- Loading indicator --}}
                            <div wire:loading wire:target="images" class="mt-2 text-primary">Đang tải ảnh lên...</div>

                            {{-- Preview ảnh MỚI --}}
                            @if ($images)
                                <div class="mt-3">
                                    <label class="form-label">Ảnh mới tải lên (preview):</label>
                                    <div class="row g-2">
                                        @foreach ($images as $index => $image)
                                            <div class="col-auto new-image-thumb" wire:key="new-img-{{ $index }}">
                                                <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail" width="100">
                                                <button type="button" class="btn btn-warning btn-sm remove-image-btn"
                                                        wire:click.prevent="removeNewImage({{ $index }})"
                                                        title="Hủy ảnh này">
                                                    &times;
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Nút Submit --}}
                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('seller.products.index') }}" class="btn btn-light me-2">Hủy</a>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            {{-- Hiển thị loading khi bấm lưu --}}
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span wire:loading.remove wire:target="save">
                                @if($productId)
                                    <i class="fas fa-save mr-2"></i> Cập nhật
                                @else
                                    <i class="fas fa-plus mr-2"></i> Lưu sản phẩm
                                @endif
                            </span>
                             <span wire:loading wire:target="save">Đang lưu...</span>
                        </button>
                    </div>
                </form>
            </div> {{-- End card-body --}}
        </div> {{-- End card --}}
    </div> {{-- End container --}}

    {{-- Thêm CSS cho nút xóa ảnh --}}
    @push('styles')
    <style>
        .existing-image-thumb, .new-image-thumb {
            position: relative;
            display: inline-block; /* Hoặc inline-flex */
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
        .remove-image-btn {
            position: absolute;
            top: -5px;
            right: -5px;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            padding: 0;
            line-height: 18px; /* Căn giữa dấu X */
            font-size: 12px;
            z-index: 10;
        }
    </style>
    @endpush
</div>

@endsection