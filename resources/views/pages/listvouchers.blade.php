@extends('layouts.app')
@section('title', 'Danh sách voucher')
@section('content')
<div class="container">
   

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
            {{ $vouchers->links('pagination::bootstrap-5') }}
        </div>
    @else
        <p>Không có voucher nào.</p>
    @endif
</div>
@endsection