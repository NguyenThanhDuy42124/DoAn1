<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @vite(['resources/css/products.css', 'resources/js/app.js'])
    <title>Thêm sản phẩm</title>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title"><i class="fas fa-plus-circle mr-2"></i>Tạo Sản Phẩm Mới</h2>
                <a href="{{ route('seller.products.index') }}" class="back-button">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </a>
            </div>
        </div>


<div class="container">
    <div class="page-header">
        <h2 class="page-title"><i class="fas fa-file-import mr-2"></i>Nhập Sản Phẩm Hàng Loạt</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-warning">
            Vui lòng kiểm tra lại dữ liệu nhập. Đảm bảo file Excel và các tệp đính kèm hợp lệ.
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="form-container">
        <form action="{{ route('seller.products.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="category_id">Áp dụng cho Danh mục</label>
                <select name="category_id" style="height: 50px;" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                    <option value="" >-- Chọn danh mục --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="excel_file">File Excel Sản phẩm (*.xlsx, *.csv)</label>
                <input type="file" name="excel_file" id="excel_file" class="form-control-file @error('excel_file') is-invalid @enderror" required>
                <small class="form-text text-danger">File Excel cần có các cột: name, price, brand, stock, description, image_1, image_2, v.v...</small>
                <a href="{{ asset('storage/Sample_excel/sample.xlsx') }}" class="btn btn-link">Tải mẫu file Excel</a>
                @error('excel_file') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="images">Các file ảnh liên quan</label>
                <input type="file" name="images[]" id="images" class="form-control-file @error('images.*') is-invalid @enderror" multiple>
                <small class="form-text text-danger">Tên file ảnh phải khớp với tên trong các cột image_X của file Excel.</small>
                <small class="form-text text-danger">Tức cột image_1 có tên là <strong>image_1.jpg</strong> thì file phải có tên là <strong>image_1.jpg</strong></small>
                @error('images.*') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary mt-4">
                <i class="fas fa-upload mr-2"></i>Thực hiện Nhập dữ liệu
            </button>
        </form>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
