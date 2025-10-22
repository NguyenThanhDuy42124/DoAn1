@extends('layouts.app')
    @section('title', 'Chỉnh sửa giỏ hàng')
    @section('content')
    <div class="container">
        <h1>Edit Item</h1>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('buyer.cart_items.update', $cartItem->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Product: {{ $cartItem->product->name }}</label>
                <input 
                    type="number"
                    name="quantity"
                    class="form-control"
                    value="{{ $cartItem->quantity }}"
                    min="1"
                    max="{{ $cartItem->product->stock }}"
                    required
                >
            </div>

            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('buyer.carts.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
