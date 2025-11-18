@extends('layouts.AdminDashBoard')
@section('content')
    <div class="container">
        <!-- Stats Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(to right, var(--primary), #004d99);">
                    <i class="fas fa-users"></i>
                    <div class="stats-value">{{ $users->count() }}</div>
                    <div class="stats-label">Người dùng</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(to right, var(--success), #1e7e34);">
                    <i class="fas fa-shopping-cart"></i>
                    <div class="stats-value">{{ $totalOrders }}</div>
                    <div class="stats-label">Đơn hàng</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(to right, var(--info), #138496);">
                    <i class="fas fa-box"></i>
                    <div class="stats-value">{{ $totalProducts }}</div>
                    <div class="stats-label">Sản phẩm</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card" style="background: linear-gradient(to right, var(--warning), #d39e00);">
                    <i class="fas fa-money-bill-wave"></i>
                    <div class="stats-value">...</div>
                    <div class="stats-label">Doanh thu</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="dashboard-card">
                    <div class="card-header">Tình trạng sản phẩm (Toàn Sàn)</div> 
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="productStatusChart"></canvas> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card">
                    <div class="card-header">Phân loại người dùng</div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="userTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Management & Notifications Row -->
        <!-- Thêm phần quản lý người dùng và gửi thông báo ở đây -->
        <!-- <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="dashboard-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                     Search và Add Buttons
                                    <span>Quản lý người dùng</span>
                                    <div>
                                        <button class="btn btn-sm btn-secondary" data-toggle="collapse"
                                            data-target="#searchPanel">
                                            <i class="fas fa-search"></i> Tìm kiếm
                                        </button>
                                        <a class="btn btn-sm btn-primary" href="{{ route('users.create') }}">
                                            <i class="fas fa-plus"></i> Thêm mới
                                        </a>
                                    </div>
                                </div>

                                
                                <div class="collapse mt-2" id="searchPanel" style="">
                                    <form method="GET" action="{{ route('admin.dashboard') }}" class="form-inline mb-3"
                                        style="align-items: left;">
                                        <div class="form-group mr-2">
                                            <input type="text" name="keyword" value="{{ request('keyword') }}"
                                                class="form-control form-control-sm" placeholder="Nhập tên người dùng"
                                                style="height: 55px;">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-filter"></i> Lọc
                                        </button>
                                    </form>
                                </div>

                          
                                @if (session()->has('message'))
    <h3 style="align-self: center">{{ session('message') }}</h3>
    @endif
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Tên người dùng</th>
                                                    <th>Email</th>
                                                    <th>Vai trò</th>
                                                    <th>Trạng thái</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($users as $user)
    <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        {{ $user->role }}
                                                         cái này nữa làm cái form select để đổi role
                                                            form này sẽ xuất hiện khi bấm nút edit ( edit sẽ thay đổi dc tên email vs role)
                                                            <select class="form-control" style="height: 50px;">
                                                            <option value="Buyer" {{ $user->role == 'buyer' ? 'selected' : '' }}>Buyer</option>
                                                            <option value="Seller" {{ $user->role == 'seller' ? 'selected' : '' }}>Seller</option>
                                                            <option value="Admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                        </select>
                                                    </td>
                                                    @if ($user->status == 'inactive')
    <td><span class="badge badge-warning">Inactive</span></td>
@else
    <td><span class="badge badge-success">Active</span></td>
    @endif
                                                    <td>
                                                        <a class="btn btn-sm btn-info"
                                                            href="{{ route('users.edit', $user->id) }}">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                            style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-danger"><i
                                                                    class="fas fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
    @endforeach
                                            </tbody>
                                        </table>

                                        Pagination
                                        {{ $users->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    </div>-->
    </div>
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    //BD1: PHÂN LOẠI NGƯỜI DÙNG 
    const userChartData = @json($userChartData ?? ['labels' => [], 'values' => []]);
    if (document.getElementById('userTypeChart') && userChartData.values.length > 0) {
        const ctxUser = document.getElementById('userTypeChart').getContext('2d');
        new Chart(ctxUser, {
            type: 'pie',
            data: {
                labels: userChartData.labels,
                datasets: [{
                    data: userChartData.values,
                    backgroundColor: ['rgba(0, 123, 255, 0.7)', 'rgba(40, 167, 69, 0.7)'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } } 
            }
        });
    }

    //BD2: TÌNH TRẠNG SẢN PHẨM
    const productStatusData = @json($productStatusData ?? ['labels' => [], 'values' => []]);

    if (document.getElementById('productStatusChart') && productStatusData.values.length > 0) {
        const ctxProduct = document.getElementById('productStatusChart').getContext('2d');
        new Chart(ctxProduct, {
            type: 'doughnut',
            data: {
                labels: productStatusData.labels,
                datasets: [{
                    data: productStatusData.values,
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.7)',  // Pending (Vàng)
                        'rgba(40, 167, 69, 0.7)',  // Approved (Xanh lá)
                        'rgba(220, 53, 69, 0.7)',  // Rejected (Đỏ)
                        'rgba(108, 117, 125, 0.7)' // Hidden (Xám)
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            // Hiển thị: "Pending: 50 (10%)"
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                const value = context.parsed;
                                label += value;

                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = (value / total * 100).toFixed(1);
                                label += ` (${percentage}%)`;
                                
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
