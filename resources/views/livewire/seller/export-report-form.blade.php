<div class="card shadow-sm mt-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Xuất Báo Cáo Doanh Thu</h6>
    </div>
    <div class="card-body">

        {{--
          - wire:submit.prevent="exportExcel" sẽ gọi hàm exportExcel() trong component
            khi form được submit và ngăn chặn việc tải lại trang.
        --}}
        <form wire:submit.prevent="exportExcel">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label for="start_date_livewire">Từ ngày:</label>
                    {{--
                      - wire:model.lazy="start_date" sẽ binding giá trị này với thuộc tính $start_date
                      - .lazy chỉ cập nhật khi người dùng 'blur' (rời khỏi) input,
                        giúp giảm số lượng request lên server.
                    --}}
                    <input type="date" id="start_date_livewire"
                           class="form-control @error('start_date') is-invalid @enderror"
                           wire:model.lazy="start_date">

                    {{-- Hiển thị lỗi validation cho 'start_date' --}}
                    @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-5">
                    <label for="end_date_livewire">Đến ngày:</label>
                    <input type="date" id="end_date_livewire"
                           class="form-control @error('end_date') is-invalid @enderror"
                           wire:model.lazy="end_date">

                    {{-- Hiển thị lỗi validation cho 'end_date' --}}
                    @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-2">
                    {{--
                      - Nút 'submit' sẽ kích hoạt wire:submit của form.
                      - wire:loading.attr="disabled" sẽ vô hiệu hóa nút khi đang xử lý (chờ server).
                      - @if($errors->any()) disabled @endif sẽ vô hiệu hóa nút nếu có bất kỳ lỗi validation nào.
                    --}}
                    <button type="submit" class="btn btn-primary w-100"
                            wire:loading.attr="disabled"
                            @if($errors->any()) disabled @endif>

                        {{-- Trạng thái bình thường --}}
                        <span wire:loading.remove wire:target="exportExcel">
                            Xuất File
                        </span>

                        {{-- Trạng thái đang tải (hiển thị spinner) --}}
                        <span wire:loading wire:target="exportExcel">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Đang xử lý...
                        </span>
                    </button>
                </div>
            </div>
        </form>

        {{-- Hiển thị lỗi tùy chỉnh (ví dụ: lỗi 15 ngày) --}}
        @error('date_range')
            <div class="text-danger small mt-2 fw-bold">{{ $message }}</div>
        @enderror

        <small class="text-muted mt-2 d-block">
            (Để trống cả 2 ngày sẽ xuất 15 đơn hàng mới nhất)
        </small>
    </div>
    <div>
        {{-- Phần body của bảng này có thể được tải bằng Livewire --}}
        <table class="table table-bordered table-hover" id="ordersTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Mã đơn hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ number_format($order->total_price, 0, ',', '.') }} VND</td>
                        <td>{{ ucfirst($order->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
