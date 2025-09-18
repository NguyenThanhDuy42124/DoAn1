@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách Voucher</h2>

    @if($vouchers->count())
        <div class="row">
            @foreach($vouchers as $voucher)
                <div class="col-md-4 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                Giảm {{ $voucher->discount_rate }}%
                            </h5>
                            <p class="card-text">
                                Điều kiện: {{ $voucher->condition ?? 'Không có' }} <br>
                                Hết hạn: {{ $voucher->expiry_date->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $vouchers->links() }}
        </div>
    @else
        <p>Không có voucher nào.</p>
    @endif
</div>
@endsection