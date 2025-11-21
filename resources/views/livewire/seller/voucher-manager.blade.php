<div>
    {{-- Header --}}
    <div class="page-header d-print-none mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">Quản lý Mã Giảm Giá (Voucher)</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <button class="btn btn-primary" wire:click="openCreateModal">
                    <i class="bi bi-plus-lg me-2"></i> Tạo Voucher Mới
                </button>
            </div>
        </div>
    </div>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Danh sách Voucher --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Mã Voucher</th>
                        <th>Loại</th>
                        <th>Giảm giá</th>
                        <th>Đã dùng / Tổng</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th class="w-1"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vouchers as $voucher)
                        <tr wire:key="voucher-{{ $voucher->id }}">
                            <td>
                                <div class="fw-bold text-primary">{{ $voucher->code }}</div>
                                <div class="text-muted small">{{ $voucher->name }}</div>
                            </td>
                            <td>
                                @if($voucher->type == 'fixed')
                                    <span class="badge bg-blue-lt">Tiền mặt</span>
                                @else
                                    <span class="badge bg-orange-lt">Phần trăm</span>
                                @endif
                            </td>
                            <td>
                                @if($voucher->type == 'fixed')
                                    {{ number_format($voucher->value, 0, ',', '.') }}₫
                                @else
                                    {{ $voucher->value }}% 
                                    @if($voucher->max_discount_amount)
                                        <br><small class="text-muted">(Tối đa: {{ number_format($voucher->max_discount_amount) }}₫)</small>
                                    @endif
                                @endif
                                <div class="text-muted small mt-1">
                                    Đơn tối thiểu: {{ number_format($voucher->min_order_value, 0, ',', '.') }}₫
                                </div>
                            </td>
                            <td>
                                <div class="clearfix">
                                    <div class="float-start">
                                        <strong>{{ $voucher->used_count }}</strong> / {{ $voucher->quantity }}
                                    </div>
                                </div>
                                <div class="progress progress-xs">
                                    <div class="progress-bar bg-primary" style="width: {{ ($voucher->used_count / max($voucher->quantity, 1)) * 100 }}%"></div>
                                </div>
                            </td>
                            <td>
                                <div class="small">
                                    <div><i class="bi bi-play-circle me-1"></i> {{ $voucher->start_date ? $voucher->start_date->format('d/m/Y H:i') : 'Ngay lập tức' }}</div>
                                    <div class="text-danger"><i class="bi bi-stop-circle me-1"></i> {{ $voucher->expiry_date ? $voucher->expiry_date->format('d/m/Y H:i') : 'Vô thời hạn' }}</div>
                                </div>
                            </td>
                            <td>
                                {{-- Toggle Switch Hoạt động ngay lập tức --}}
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           wire:change="toggleStatus({{ $voucher->id }})"
                                           {{ $voucher->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <button class="btn btn-white btn-sm" wire:click="openEditModal({{ $voucher->id }})">
                                        Sửa
                                    </button>
                                    @if($voucher->used_count == 0)
                                        <button class="btn btn-outline-danger btn-sm" 
                                                wire:confirm="Bạn có chắc chắn muốn xóa voucher này không?"
                                                wire:click="delete({{ $voucher->id }})">
                                            Xóa
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">Chưa có mã giảm giá nào.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $vouchers->links() }}
        </div>
    </div>

    {{-- MODAL CREATE / EDIT --}}
    @if($showModal)
    <div class="modal modal-blur fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? 'Chỉnh sửa Voucher' : 'Tạo Voucher Mới' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label required">Mã Voucher</label>
                            {{-- FIX 1: Dùng wire:model.blur để đảm bảo dữ liệu được cập nhật --}}
                            <input type="text" wire:model.live="code" 
                                class="form-control @error('code') is-invalid @enderror" 
                                placeholder="VD: SALE50" style="text-transform: uppercase;">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Tên chương trình</label>
                            {{-- FIX 1: Dùng wire:model.blur --}}
                            <input type="text" wire:model.blur="name" 
                                class="form-control @error('name') is-invalid @enderror" 
                                placeholder="VD: Giảm giá mùa hè">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label required">Loại giảm giá</label>
                            {{-- Select dùng .live để đổi UI ngay lập tức --}}
                            <select wire:model.live="type" class="form-select">
                                <option value="fixed">Giảm tiền mặt (VNĐ)</option>
                                <option value="percent">Giảm phần trăm (%)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Giá trị giảm</label>
                            <div class="input-group">
                                <input type="number" wire:model.blur="value" class="form-control @error('value') is-invalid @enderror" min="1">
                                <span class="input-group-text">{{ $type == 'fixed' ? 'VNĐ' : '%' }}</span>
                            </div>
                            @error('value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Số lượng phát hành</label>
                            <input type="number" wire:model.blur="quantity" class="form-control @error('quantity') is-invalid @enderror" min="1">
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    @if($type == 'percent')
                        <div class="mb-3">
                            <label class="form-label">Giảm tối đa (VNĐ)</label>
                            <input type="number" wire:model.blur="max_discount_amount" class="form-control" placeholder="Để trống nếu không giới hạn">
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                        <input type="number" wire:model.blur="min_order_value" class="form-control" value="0">
                    </div>

                    {{-- FIX 2: Cải thiện UX Ngày tháng --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu</label>
                            {{-- Thêm gợi ý cho người dùng --}}
                            <input type="datetime-local" wire:model.blur="start_date" class="form-control">
                            <small class="form-hint text-muted">Bỏ trống nếu muốn bắt đầu ngay lập tức.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="datetime-local" wire:model.blur="expiry_date" class="form-control @error('expiry_date') is-invalid @enderror">
                            <small class="form-hint text-muted">Bỏ trống nếu voucher không bao giờ hết hạn.</small>
                            @error('expiry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-link link-secondary me-2" wire:click="closeModal">Hủy</button>
                        <button type="submit" class="btn btn-primary">
                            <div wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2"></div>
                            {{ $isEditMode ? 'Cập nhật' : 'Tạo Voucher' }}
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>