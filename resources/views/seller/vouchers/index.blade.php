@extends('layouts.SellerDashBoard')
@section('content')
<div class="container">
  

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
         <div class="page-header mt-4">
    <div class="d-flex justify-content-between align-items-center">
        
        <div class="mb-3 d-flex">
   
    <a href="{{ route('vouchers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i>Thêm Voucher
    </a>
</div>
   <div class="mb-3">
            <a href="{{ route('seller.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home mr-2"></i>Quay lại
            </a>
        </div>

  
    </div>
</div>

    @if($vouchers->count())
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>STT</th>
                    <th>Giảm giá</th>
                    <th>Điều kiện</th>
                    <th>Ngày hết hạn</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vouchers as $voucher)
                    <tr>
                        <td>{{ $voucher->id }}</td>
                        <td>{{ $voucher->discount_rate }}%</td>
                        <td>{{ $voucher->condition ?? 'Không có' }}</td>
                        <td>{{ $voucher->expiry_date->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-primary">
                                Sửa
                            </a>
                            <form action="{{ route('vouchers.destroy', $voucher->id) }}" 
                                  method="POST" class="d-inline"
                                  >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    Xóa
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $vouchers->links() }}
        </div>
    @else
        <p>Hiện chưa có voucher nào.</p>
    @endif
</div>
@endsection