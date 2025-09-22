<!DOCTYPE html>
<html>
<head>
    <title>Edit Cart</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Edit Cart</h1>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($cartItems->isEmpty())
            <p>Your cart is empty.</p>
        @else
            @foreach ($cartItems as $item)
                <form action="{{ route('buyer.carts.update', $cart->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Product: {{ $item->product->name }}</label>
                        <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                    </div>
                    <div class="mb-3">
                        <label for="quantity-{{ $item->id }}" class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="quantity-{{ $item->id }}"
                               class="form-control" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" required>
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('buyer.carts.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            @endforeach
        @endif
    </div>
</body>
</html>