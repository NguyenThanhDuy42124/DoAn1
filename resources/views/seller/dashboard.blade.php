@extends('layouts.SellerDashBoard')
@section('content')
<div class="container">
    
    <div class="page-header mt-4 mb-4">
        <h1 class="h2">Tổng quan</h1>
        <p class="text-muted">Chào mừng trở lại, đây là những gì đang diễn ra hôm nay.</p>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100 border-start border-primary border-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="text-muted text-uppercase small fw-bold">Doanh thu (Hôm nay)</div>
                    <h3 class="fw-bold mb-0 mt-2">{{ number_format($todayRevenue ?? 0, 0, ',', '.') }} đ</h3>
                </div>
                <div class="fs-2 text-primary">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>
</div>

        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm h-100 border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="text-muted text-uppercase small fw-bold">Đơn hàng (Hôm nay)</div>
                            <h3 class="fw-bold mb-0 mt-2">{{ $todayOrderCount ?? 0 }}</h3>
                        </div>
                        <div class="fs-2 text-success">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100 border-start border-info border-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="text-muted text-uppercase small fw-bold">Sản phẩm đang bán</div>
                    
                    <h3 class="fw-bold mb-0 mt-2">{{ $approvedProductCount }}</h3>
                    
                </div>
                <div class="fs-2 text-info">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>
    </div>
</div>
        <div class="col-md-6 col-lg-3">
    <div class="card shadow-sm h-100 border-start border-warning border-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="text-muted text-uppercase small fw-bold">Sản phẩm chờ duyệt</div>
                    
                    <h3 class="fw-bold mb-0 mt-2">{{ $pendingProductCount }}</h3>
                    
                </div>
                <div class="fs-2 text-warning">
                    <i class="fas fa-pause-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8">
    <div class="card shadow-sm h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><i class="fas fa-chart-bar me-2"></i>Báo cáo doanh thu (7 ngày qua)</h5>
            <a href="{{ route('seller.reports.index') }}" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 350px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

        <div class="col-lg-4">
    <div class="card shadow-sm h-100">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-list-ul me-2"></i>Đơn hàng mới nhất</h5>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                
                @forelse($latestOrders as $order)
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">Đơn hàng #{{ $order->id }}</h6>
                            <small class="text-muted">{{ $order->created_at->locale('vi')->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1 fw-bold text-success">{{ number_format($order->total_price, 0, ',', '.') }} đ</p>
                        <small>{{ $order->buyer_name }}</small>
                    </a>
                @empty
                    <div class="list-group-item">
                        <p class="text-muted mb-0 text-center">Không có đơn hàng mới nào.</p>
                    </div>
                @endforelse

            </div>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('seller.orders.index') }}">Xem tất cả đơn hàng</a>
        </div>
    </div>
</div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // Hàm để định dạng số tiền
        function formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
        }

        // 1. Lấy dữ liệu động từ API Laravel
        fetch('{{ route("seller.api.revenue.report") }}') // <-- Gọi route đã tạo
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                // 2. Xử lý dữ liệu
                const labels = data.map(item => {
                    const date = new Date(item.date);
                    return `${date.getDate()}/${date.getMonth() + 1}`; // Format: "25/10"
                });
                
                const revenueData = data.map(item => item.revenue);

                // 3. Vẽ biểu đồ
                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Doanh thu',
                            data: revenueData,
                            backgroundColor: 'rgba(0, 123, 255, 0.5)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 1,
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) return (value / 1000000) + ' Tr';
                                        if (value >= 1000) return (value / 1000) + ' k';
                                        return formatCurrency(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Doanh thu: ' + formatCurrency(context.parsed.y);
                                    }
                                }
                            },
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Lỗi khi tải dữ liệu biểu đồ:', error);
                // Hiển thị lỗi nếu không tải được
                const chartContainer = document.getElementById('revenueChart').parentElement;
                chartContainer.innerHTML = `<div class="d-flex h-100 justify-content-center align-items-center text-danger">
                                                <span>Không thể tải dữ liệu biểu đồ.</span>
                                            </div>`;
            });
    });
</script>
@endsection