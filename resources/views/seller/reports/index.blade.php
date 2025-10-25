@extends('layouts.SellerDashBoard') {{-- Đảm bảo tên layout của bạn chính xác --}}

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Thống kê & Báo cáo</h1>
</div>

<div class="row">

    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top 5 Khách hàng (Theo doanh thu)</h6>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 350px;">
                    <canvas id="topCustomersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Doanh thu theo thời gian</h6>
                <div class="btn-group btn-group-sm" id="revenue-filter-group">
                    <button type="button" class="btn btn-outline-primary active" data-range="7d" data-type="bar">7 ngày</button>
                    <button type="button" class="btn btn-outline-primary" data-range="1m" data-type="bar">1 tháng</button>
                    <button type="button" class="btn btn-outline-primary" data-range="1y" data-type="line">1 năm</button>
                </div>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 350px;">
                    <canvas id="dynamicRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div> <div class="card shadow-sm mt-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Xuất Báo Cáo Doanh Thu</h6>
    </div>
    <div class="card-body">
        <form action="#" method="GET"> 
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label for="start_date">Từ ngày:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control">
                </div>
                <div class="col-md-5">
                    <label for="end_date">Đến ngày:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100" disabled>Xuất File</button>
                </div>
            </div>
        </form>
        <div id="date-range-error" class="text-danger small mt-2 fw-bold"></div>
        <small class="text-muted mt-2 d-block">(Chức năng này đang được phát triển)</small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // --- HÀM HELPER (Giữ nguyên) ---
        function formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
        }
        function formatShort(value) {
            if (value >= 1000000) return (value / 1000000) + ' Tr';
            if (value >= 1000) return (value / 1000) + ' k';
            return formatCurrency(value);
        }

        // --- BIỂU ĐỒ 1: TOP 5 KHÁCH HÀNG (MỚI) ---
        const topCustomerLabels = @json($topCustomerLabels ?? []);
        const topCustomerValues = @json($topCustomerValues ?? []);

        if (document.getElementById('topCustomersChart')) {
            // Kiểm tra nếu không có dữ liệu
            if (topCustomerValues.length === 0) {
                 document.getElementById('topCustomersChart').parentElement.innerHTML = '<div class="d-flex h-100 justify-content-center align-items-center text-muted">Chưa có dữ liệu khách hàng.</div>';
            } else {
                const ctxTopCustomers = document.getElementById('topCustomersChart').getContext('2d');
                new Chart(ctxTopCustomers, {
                    type: 'bar', // Biểu đồ cột
                    data: {
                        labels: topCustomerLabels, 
                        datasets: [{
                            label: 'Tổng chi tiêu', 
                            data: topCustomerValues,
                            backgroundColor: [
                                'rgba(0, 123, 255, 0.7)',
                                'rgba(40, 167, 69, 0.7)',
                                'rgba(255, 193, 7, 0.7)',
                                'rgba(220, 53, 69, 0.7)',
                                'rgba(23, 162, 184, 0.7)'
                            ],
                            borderColor: '#fff',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        indexAxis: 'y', // <-- Biến thành biểu đồ cột NGANG
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { // Trục X (tiền)
                                beginAtZero: true,
                                ticks: { callback: formatShort }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: { 
                                callbacks: { 
                                    label: (context) => 'Tổng chi tiêu: ' + formatCurrency(context.parsed.x)
                                } 
                            }
                        }
                    }
                });
            }
        }

        // --- BIỂU ĐỒ 2: DOANH THU ĐỘNG (Giữ nguyên) ---
        const ctxDynamic = document.getElementById('dynamicRevenueChart').getContext('2d');
        const dynamicChart = new Chart(ctxDynamic, {
            type: 'bar', 
            data: { labels: [], datasets: [{
                label: 'Doanh thu', data: [],
                backgroundColor: 'rgba(40, 167, 69, 0.5)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1, borderRadius: 5,
                fill: true, tension: 0.1 
            }]},
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { callback: formatShort } } },
                plugins: {
                    tooltip: { callbacks: { label: (context) => 'Doanh thu: ' + formatCurrency(context.parsed.y) } },
                    legend: { display: false }
                }
            }
        });

        function fetchAndUpdateChart(range, chartType) {
            const url = `{{ route("seller.api.revenue.report") }}`;
            fetch(`${url}?range=${range}`)
                .then(response => response.json())
                .then(data => {
                    dynamicChart.config.type = chartType; 
                    dynamicChart.data.labels = data.labels;
                    dynamicChart.data.datasets[0].data = data.values;
                    if(chartType === 'line') {
                        dynamicChart.data.datasets[0].backgroundColor = 'rgba(40, 167, 69, 0.1)';
                        dynamicChart.data.datasets[0].borderColor = 'rgba(40, 167, 69, 1)';
                    } else {
                        dynamicChart.data.datasets[0].backgroundColor = 'rgba(40, 167, 69, 0.5)';
                        dynamicChart.data.datasets[0].borderColor = 'rgba(40, 167, 69, 1)';
                    }
                    dynamicChart.update();
                })
                .catch(error => {
                    console.error('Lỗi tải biểu đồ động:', error);
                    document.getElementById('dynamicRevenueChart').parentElement.innerHTML = '<span class="text-danger">Không thể tải dữ liệu.</span>';
                });
        }

        const filterButtons = document.querySelectorAll('#revenue-filter-group button');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const range = this.dataset.range;
                const type = this.dataset.type;
                fetchAndUpdateChart(range, type);
            });
        });
        
        fetchAndUpdateChart('7d', 'bar');

        // --- PHẦN 3: VALIDATE DATE RANGE (Giữ nguyên) ---
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const errorContainer = document.getElementById('date-range-error');
        
        function validateDateRange() {
            const startVal = startDateInput.value;
            const endVal = endDateInput.value;
            const MAX_RANGE_DAYS = 15;

            errorContainer.textContent = '';
            if (startVal && endVal) {
                const startDate = new Date(startVal);
                const endDate = new Date(endVal);

                if (endDate < startDate) {
                    errorContainer.textContent = 'Lỗi: Ngày kết thúc không thể trước ngày bắt đầu.';
                    return;
                }
                const diffMs = endDate.getTime() - startDate.getTime();
                const diffDays = diffMs / (1000 * 60 * 60 * 24);

                if (diffDays >= MAX_RANGE_DAYS) {
                    errorContainer.textContent = `Lỗi: Chỉ được xuất báo cáo trong khoảng ${MAX_RANGE_DAYS} ngày hoặc ít hơn.`;
                }
            }
        }
        startDateInput.addEventListener('change', validateDateRange);
        endDateInput.addEventListener('change', validateDateRange);

    });
</script>

@endsection