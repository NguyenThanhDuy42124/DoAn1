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
    <title>Danh sách sản phẩm</title>
   </head>

<body>
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title"><i class="fas fa-boxes mr-2"></i>Danh sách sản phẩm</h2>
                <div class="mb-3 d-flex">
                <a href="{{ route('seller.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Thêm sản phẩm
                </a>
                <a href="{{ route('seller.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-home mr-2"></i>Quay lại
                </a>
                </div>
            </div>
        </div>

        @if($products && count($products) > 0)
            <div class="row">
                @foreach($products as $product)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 product-card">
                            @if($product->status === 'active')
                                <span class="status-badge status-active">Đang bán</span>
                            @else
                                <span class="status-badge status-inactive">Ngừng bán</span>
                            @endif
                            
                            <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' }}" 
                                 alt="{{ $product->name }}" class="card-img-top product-img">
    
                            <div class="card-body product-body">
                                <h5 class="card-title product-title">{{ $product->name }}</h5>
                                
                                <div class="product-meta">
                                    <div><i class="fas fa-box mr-2"></i>Tồn kho: {{ $product->stock }}</div>
                                    <div><i class="fas fa-tag mr-2"></i>Thương hiệu: {{ $product->brand ?? 'Chưa có' }}</div>
                                </div>
    
                                <div class="product-price mb-3">
                                    {{ number_format($product->price, 0, ',', '.') }}₫
                                </div>
    
                                <div class="d-flex flex-column">
                                    <a href="{{ route('seller.products.edit', $product->id) }}" 
                                       class="btn btn-primary mb-2">
                                        <i class="fas fa-edit mr-2"></i>Sửa
                                    </a>
    
                                    <form action="{{ route('seller.products.destroy', $product->id) }}" 
                                          method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-block">
                                            <i class="fas fa-trash mr-2"></i>Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h4>Chưa có sản phẩm nào</h4>
                <p>Hãy thêm sản phẩm đầu tiên của bạn để bắt đầu bán hàng</p>
                <a href="{{ route('seller.products.create') }}" class="btn btn-success mt-3">
                    <i class="fas fa-plus mr-2"></i>Thêm sản phẩm đầu tiên
                </a>
            </div>
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>