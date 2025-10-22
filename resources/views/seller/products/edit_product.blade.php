@extends('layouts.SellerDashBoard')
@section('content')
 <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title"><i class="fas fa-edit mr-2"></i>Chỉnh sửa Sản Phẩm</h2>
                <a href="{{ route('seller.products.index') }}" class="back-button">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </a>
            </div>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="form-container">
            <form action="{{ route('seller.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="category_id">Danh mục</label>
                    @if(isset($categories) && count($categories) > 0)
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Không có danh mục nào khả dụng.
                    </div>
                    <input type="hidden" name="category_id" value="{{ $product->category_id }}">
                    <p class="text-muted">Danh mục hiện tại: {{ $product->category->name ?? 'Không xác định' }}</p>
                    @endif
                </div>

                <div class="form-group">
                    <label for="name">Tên sản phẩm</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="price">Giá</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" required>
                </div>

                <div class="form-group">
                    <label for="brand">Thương hiệu</label>
                    <input type="text" name="brand" id="brand" class="form-control" value="{{ old('brand', $product->brand) }}">
                </div>

                <div class="form-group">
                    <label for="stock">Tồn kho</label>
                    <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', $product->stock) }}">
                </div>

                <div class="form-group">
                    <label for="status">Trạng thái</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="status_active" value="active" {{ old('status', $product->status) == 'active' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_active">
                                Đang bán
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="status_inactive" value="inactive" {{ old('status', $product->status) == 'inactive' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_inactive">
                                Ngừng bán
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea name="description" id="description" class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label for="images">Thêm ảnh mới</label>
                    <input type="file" name="images[]" id="images" class="form-control-file" multiple>
                </div>
                <div class="form-group">
                    <label>Ảnh sản phẩm hiện tại</label>
                    <div class="row">
                        @foreach($product->images as $image)
                        <div class="col-md-3 mb-3">
                            <div class="image-preview-container">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid" alt="Product Image">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="deleted_images[]" value="{{ $image->id }}" id="deleteImage-{{ $image->id }}">
                                    <label class="form-check-label" for="deleteImage-{{ $image->id }}">
                                        Xóa ảnh này
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('seller.products.index') }}" class="btn btn-light mr-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Cập nhật sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection