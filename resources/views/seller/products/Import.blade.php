@extends('layouts.SellerDashBoard')
@section('content')


<div class="container">
    <div class="page-header">
         <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title"><i class="fas fa-file-import mr-2"></i>Nhập Sản Phẩm Hàng Loạt</h2>
        <a href="{{ route('seller.products.index') }}" class="back-button">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </a>
                 </div>
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
                <input type="file" accept="image/*" name="images[]" id="images" class="form-control-file @error('images.*') is-invalid @enderror" multiple>
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
@endsection
