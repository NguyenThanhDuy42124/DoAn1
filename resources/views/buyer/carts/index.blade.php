@extends('layouts.app')
    @section('title', 'Giỏ hàng')
    @section('content')
    <div class="container">
        <h1>My Cart</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($cartItems->isEmpty())
            <p>Your cart is empty.</p>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        @php
                            // Lấy stock hiện tại của sản phẩm
                            $maxStock = $item->product->stock;
                            // Nếu số lượng trong cart > stock thì set tạm quantity = stock
                            $displayQuantity = min($item->quantity, $maxStock);
                            // Kiểm tra hết hàng
                            $outOfStock = $maxStock == 0;
                        @endphp

                        <tr class="{{ $outOfStock ? 'text-muted' : '' }}">
                            <td>{{ $item->product->name }}</td>
                            <td>{{ number_format($item->price, 0, ',', '.') }}₫</td>
                            <td>{{ $displayQuantity }}</td>
                            <td>{{ number_format($item->price * $displayQuantity, 0, ',', '.') }}₫</td>
                            <td>
                                @if($outOfStock)
                                    <span class="badge bg-danger">Hết hàng, vui lòng xoá</span>
                                    <form action="{{ route('buyer.cart_items.destroy', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete Item</button>
                                    </form>
                                @else
                                    <a href="{{ route('buyer.cart_items.edit', $item->id) }}" class="btn btn-primary btn-sm">Edit</a>

                                    <form action="{{ route('buyer.cart_items.destroy', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete Item</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p>
            <form action="{{ route('buyer.checkouts.checkout') }}" method="POST">
                @csrf
                @php
                $hasItems = $cartItems->where('quantity', '>', 0)->count() > 0;
                @endphp

                <button type="submit" class="btn btn-success" @if(!$hasItems) disabled @endif>
                    Checkout
                </button>
            </form>
        </p>

        <a href="{{ route('products.list') }}" class="btn btn-primary">Continue Shopping</a>
    </div>
    @endsection
