<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h1>Giỏ hàng của tôi</h1>

    @if (empty($cartItems))
        <p>Your cart is empty.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" wire:model.live="selectedItems" value="all" /> Select All</th> <!-- Optional select all -->
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
                        $maxStock = $item['product']['stock'];
                        $outOfStock = $maxStock == 0;
                        $itemTotal = $item['price'] * ($quantities[$item['id']] ?? 0);
                    @endphp

                    <tr class="{{ $outOfStock ? 'text-muted' : '' }}">
                        <td>
                            <input type="checkbox" wire:model.live="selectedItems" value="{{ $item['id'] }}" />
                        </td>
                        <td>{{ $item['product']['name'] }}</td>
                        <td>{{ number_format($item['price'], 0, ',', '.') }}₫</td>
                        <td>
                            @if($outOfStock)
                                <span class="badge bg-danger">Hết hàng</span>
                            @else
                                <input type="number" wire:model.live="quantities.{{ $item['id'] }}" min="1" max="{{ $maxStock }}" class="form-control w-50" />
                            @endif
                        </td>
                        <td>{{ number_format($itemTotal, 0, ',', '.') }}₫</td>
                        <td>
                            <button wire:click="removeItem({{ $item['id'] }})" class="btn btn-danger btn-sm">Xóa</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Tổng tiền (selected):</strong></td>
                    <td><strong>{{ number_format($this->totalPrice, 0, ',', '.') }}₫</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <button wire:click="checkout" class="btn btn-success" @if(empty($selectedItems)) disabled @endif>Thanh toán</button>
        <a href="{{ route('products.list') }}" class="btn btn-primary">Tiếp tục mua hàng</a>
    @endif

    <!-- JS toast cho notify (optional, dùng wireui hoặc alpine) -->
    
</div>