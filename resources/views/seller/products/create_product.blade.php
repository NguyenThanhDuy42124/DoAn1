@extends('layouts.SellerDashBoard')
@section('content')
<div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title"><i class="fas fa-plus-circle mr-2"></i>Tạo Sản Phẩm Mới</h2>
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
            <form action="{{ route('seller.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="seller_id" value="{{ auth()->id() }}">

                <div class="form-group">
                    <label for="category_id">Danh mục</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="name">Tên sản phẩm</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="price">Giá</label>
                    <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
                </div>

                <div class="form-group">
                    <label for="brand">Thương hiệu</label>
                    <input type="text" name="brand" id="brand" class="form-control" value="{{ old('brand') }}">
                </div>

                <div class="form-group">
                    <label for="stock">Tồn kho</label>
                    <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', 0) }}">
                </div>

                <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="form-group">
                    <label for="images">Ảnh sản phẩm</label>
                    <input type="file" name="images[]" id="images" class="form-control-file" multiple>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('seller.products.index') }}" class="btn btn-light mr-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Lưu sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection