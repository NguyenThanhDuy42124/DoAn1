<form action="{{ route('seller.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name">Tên sản phẩm</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
    </div>

    <div class="form-group">
        <label for="price">Giá</label>
        <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" required>
    </div>

    <div class="form-group">
        <label for="image">Ảnh sản phẩm</label>
        <input type="file" name="image" class="form-control">
        @if($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" width="100" class="mt-2">
        @endif
    </div>

    <button type="submit" class="btn btn-success">Cập nhật</button>
</form>
