@extends('project_1.admin.layouts.layout')
@section('css')
 <link rel="stylesheet" href="{{asset('css/project_1/chart.css')}}">
 <style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap');

    .dashboard-wrap {
        font-family: 'Outfit', sans-serif;
    }

    .kpi-card {
        background: linear-gradient(135deg, #1f8a70 0%, #2bb3a2 100%);
        border-radius: 10px;
        padding: 20px;
        color: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        cursor: pointer;
    }
    
    .kpi-card:hover {
        transform: translateY(-5px);
    }
    
    .kpi-card.revenue {
        background: linear-gradient(135deg, #1f8a70 0%, #2bb3a2 100%);
    }
    
    .kpi-card.orders {
        background: linear-gradient(135deg, #ff8f2a 0%, #ff6b35 100%);
    }
    
    .kpi-card.customers {
        background: linear-gradient(135deg, #2185d0 0%, #4fb0ff 100%);
    }
    
    .kpi-card.products {
        background: linear-gradient(135deg, #23a26d 0%, #6bd58f 100%);
    }
    
    .kpi-card.avg-value {
        background: linear-gradient(135deg, #e7a528 0%, #f2c94c 100%);
    }
    
    .kpi-value {
        font-size: 2rem;
        font-weight: bold;
        margin: 10px 0;
    }
    
    .kpi-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .kpi-icon {
        font-size: 2.5rem;
        opacity: 0.3;
        float: right;
    }
    
    .status-chart {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .status-badge {
        flex: 1;
        min-width: 150px;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        color: white;
        font-weight: bold;
    }
    
    .status-completed {
        background-color: #28a745;
    }
    
    .status-pending {
        background-color: #ffc107;
        color: #333;
    }
    
    .status-approved {
        background-color: #17a2b8;
    }
    
    .status-failed {
        background-color: #dc3545;
    }
    
    .top-customers-table {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .product-row {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .product-row:hover {
        background-color: #f8f9fa;
    }

    .revenue-7-card {
        border: 0;
        box-shadow: 0 8px 24px rgba(20, 30, 40, 0.08);
    }

    .revenue-7-card .card-header {
        background: linear-gradient(135deg, #0f766e 0%, #16a085 100%);
        color: #fff;
        border-bottom: 0;
        font-weight: 600;
    }

    .revenue-rows {
        display: grid;
        gap: 12px;
    }

    .revenue-row {
        display: grid;
        grid-template-columns: 84px 1fr auto auto;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 10px;
        background: #f6f8fb;
    }

    .rev-date {
        font-weight: 600;
        color: #0f172a;
        font-size: 0.85rem;
    }

    .rev-bar {
        height: 10px;
        background: #e1e7ef;
        border-radius: 999px;
        overflow: hidden;
    }

    .rev-bar span {
        display: block;
        height: 100%;
        background: linear-gradient(90deg, #ff7a45 0%, #ffb347 100%);
        border-radius: 999px;
    }

    .rev-value {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    .rev-orders {
        font-size: 0.8rem;
        color: #64748b;
        white-space: nowrap;
    }

    .chart-card {
        border: 0;
        box-shadow: 0 8px 24px rgba(20, 30, 40, 0.08);
    }

    .chart-card .card-header {
        background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);
        color: #fff;
        border-bottom: 0;
        font-weight: 600;
    }

    .chart-wrap {
        min-height: 240px;
    }

    @media (max-width: 576px) {
        .revenue-row {
            grid-template-columns: 1fr;
            align-items: start;
        }
    }
 </style>
@endsection
@section('content')
    

    <div class="container mt-4 dashboard-wrap">
        <h4 class="mr-2 font-weight-bold mb-4">
            <i class="fas fa-chart-bar"></i> Thống kê Bán Hàng 

        </h4>

        <form action="{{ route('statistics.filter') }}" method="GET" class="form-inline mb-4">
            <div class="form-row align-items-center">
                <div class="col-auto">
                <label for="from" class="">Từ ngày:</label>

                <input type="date" name="from" id="from" class="form-control mb-2"style="width:30% " required value="{{ request('from') }}">
                </div>
                <div class="col-auto">
                <label for="to" class="mr-2 font-weight-bold">Đến ngày:</label>
                <input type="date" name="to" id="to" class="form-control mb-2" style="width:30% " required value="{{ request('to') }}">
                </div>
                <div class="col-auto">
                <button type="submit" class="btn btn-primary mb-2"><i class="fas fa-search"></i> Tìm kiếm</button>
                </div>
            </div>
        </form>

        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="kpi-card revenue">
                    <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="kpi-label">Tổng Doanh Thu</div>
                    <div class="kpi-value">{{ number_format($kpis['total_revenue'], 0, ',', '.') }}đ</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="kpi-card orders">
                    <div class="kpi-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="kpi-label">Tổng Đơn Hàng</div>
                    <div class="kpi-value">{{ $kpis['total_orders'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="kpi-card customers">
                    <div class="kpi-icon"><i class="fas fa-users"></i></div>
                    <div class="kpi-label">Tổng Khách Hàng</div>
                    <div class="kpi-value">{{ $kpis['total_customers'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="kpi-card products">
                    <div class="kpi-icon"><i class="fas fa-box"></i></div>
                    <div class="kpi-label">Sản Phẩm Bán Ra</div>
                    <div class="kpi-value">{{ $kpis['total_products_sold'] }}</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="kpi-card avg-value">
                    <div class="kpi-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="kpi-label">Giá Trung Bình</div>
                    <div class="kpi-value">{{ number_format($kpis['avg_order_value'], 0, ',', '.') }}đ</div>
                </div>
            </div>
        </div>

        <!-- Order Status Stats -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-chart-pie"></i> Trạng Thái Đơn Hàng
            </div>
            <div class="card-body">
                <div class="status-chart">
                    <div class="status-badge status-completed">
                        <div style="font-size: 1.5rem;">{{ $orderStats['completed'] }}</div>
                        <div>Đã Thanh Toán</div>
                    </div>
                    <div class="status-badge status-pending">
                        <div style="font-size: 1.5rem;">{{ $orderStats['pending'] }}</div>
                        <div>Chưa Thanh Toán</div>
                    </div>
                    <div class="status-badge status-approved">
                        <div style="font-size: 1.5rem;">{{ $orderStats['approved'] }}</div>
                        <div>Đã Duyệt</div>
                    </div>
                    <div class="status-badge status-failed">
                        <div style="font-size: 1.5rem;">{{ $orderStats['failed'] }}</div>
                        <div>Thất Bại</div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $maxRevenue = collect($dailyStats)->max('total_revenue') ?: 1;
        @endphp

        <div class="row mb-4">
            <div class="col-lg-5 mb-3">
                <div class="card revenue-7-card">
                    <div class="card-header">
                        📅 Doanh thu 7 ngày gần nhất
                    </div>
                    <div class="card-body">
                        <div class="revenue-rows">
                            @foreach($dailyStats as $row)
                                @php
                                    $pct = (int) round((($row['total_revenue'] ?? 0) / $maxRevenue) * 100);
                                @endphp
                                <div class="revenue-row">
                                    <div class="rev-date">{{ \Carbon\Carbon::parse($row['date'])->format('d/m') }}</div>
                                    <div class="rev-bar"><span style="width: {{ $pct }}%"></span></div>
                                    <div class="rev-value">{{ number_format($row['total_revenue'] ?? 0, 0, ',', '.') }}đ</div>
                                    <div class="rev-orders">{{ $row['total_orders'] }} đơn</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 mb-3">
                <div class="card chart-card">
                    <div class="card-header">Xu hướng doanh thu</div>
                    <div class="card-body">
                        <div class="chart-wrap">
                            <canvas id="revenueChart" height="140"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Top 5 Products -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-star"></i> Top 5 Sản Phẩm Bán Chạy
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên Sản Phẩm</th>
                                    <th>Số Lượng Bán</th>
                                    <th>Doanh Thu (VNĐ)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $index => $product)
                                    <tr class="product-row">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $product->product_name }}</td>
                                        <td><span class="badge bg-primary">{{ $product->total_quantity }}</span></td>
                                        <td>{{ number_format($product->total_revenue, 0, ',', '.') }}đ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Customers -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-crown"></i> Top Khách Hàng
                    </div>
                    <div class="card-body top-customers-table">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tên KH</th>
                                    <th>Đơn Hàng</th>
                                    <th>Chi Tiêu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCustomers as $customer)
                                    <tr>
                                        <td>
                                            <small><strong>{{ substr($customer->name, 0, 10) }}</strong></small>
                                        </td>
                                        <td><span class="badge bg-info">{{ $customer->total_orders }}</span></td>
                                        <td><small>{{ number_format($customer->total_spent, 0, ',', '.') }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Chưa có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('statistics.export') }}" method="GET" class="mb-3 mt-4">
            <input type="hidden" name="from" value="{{ request('from') }}">
            <input type="hidden" name="to" value="{{ request('to') }}">
            <button type="submit" class="btn btn-success" style=" margin-top:10px"><i class="fas fa-file-excel text-success"></i> Xuất Excel</button>
        </form>
    </div>



@endsection
@@
@section('js')
@parent
<!-- Chart.js CDN for revenue chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Custom chart data -->
<script>
    const labels = {!! json_encode(collect($dailyStats)->pluck('date')) !!};
    const data = {!! json_encode(collect($dailyStats)->pluck('total_revenue')) !!};
</script>
<script src="{{ asset('js/project_1/chart.js') }}"></script>

@endsection
