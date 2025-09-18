<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/products.css', 'resources/js/app.js'])
    <title>Chỉnh sửa sản phẩm</title>
   </head>
<body>
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
                    <label for="image">Ảnh sản phẩm</label>
                    <input type="file" name="image" id="image" class="form-control-file">
                    @if($product->image)
                        <div class="mt-3">
                            <p>Ảnh hiện tại:</p>
                            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="image-preview">
                        </div>
                    @endif
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

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>