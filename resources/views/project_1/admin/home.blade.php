@extends('project_1.admin.layouts.layout')

@section('css')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap');

    .dashboard-v3 {
        font-family: 'Be Vietnam Pro', sans-serif;
        --ink: #102a43;
        --muted: #627d98;
        --bg-soft: #f4f7fb;
        --card: #ffffff;
        --primary: #0f766e;
        --accent: #f59e0b;
        --danger: #dc2626;
    }

    .dashboard-shell {
        background: radial-gradient(1200px 500px at 100% -120px, #b9f1dd 0%, transparent 60%),
            radial-gradient(900px 400px at -80px 80px, #d6e6ff 0%, transparent 60%),
            var(--bg-soft);
        border-radius: 18px;
        padding: 24px;
    }

    .hero {
        background: linear-gradient(120deg, #0f766e 0%, #0ea5e9 100%);
        color: #fff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 10px 25px rgba(16, 42, 67, 0.16);
    }

    .hero h3 {
        margin-bottom: 6px;
        font-weight: 800;
    }

    .hero p {
        margin-bottom: 0;
        opacity: 0.9;
    }

    .filter-card,
    .stat-card,
    .chart-card,
    .table-card,
    .stock-card {
        background: var(--card);
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(16, 42, 67, 0.08);
    }

    .filter-card {
        padding: 14px;
    }

    .stat-card {
        color: #fff;
        position: relative;
        overflow: hidden;
        min-height: 128px;
        padding: 16px;
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .stat-card::after {
        content: '';
        position: absolute;
        right: -20px;
        top: -20px;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.22);
    }

    .stat-title {
        font-size: 0.86rem;
        opacity: 0.95;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1;
    }

    .stat-note {
        font-size: 0.8rem;
        margin-top: 8px;
        opacity: 0.9;
    }

    .stat-1 { background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); }
    .stat-2 { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); }
    .stat-3 { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); }
    .stat-4 { background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); }
    .stat-5 { background: linear-gradient(135deg, #be123c 0%, #f43f5e 100%); }
    .stat-6 { background: linear-gradient(135deg, #15803d 0%, #22c55e 100%); }
    .stat-7 { background: linear-gradient(135deg, #4b5563 0%, #6b7280 100%); }

    .chart-card .card-header,
    .table-card .card-header,
    .stock-card .card-header {
        background: transparent;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 700;
        color: var(--ink);
        padding: 14px 16px;
    }

    .chart-card .card-body,
    .table-card .card-body,
    .stock-card .card-body {
        padding: 14px 16px;
    }

    .chart-h {
        position: relative;
        height: 320px;
        min-height: 0;
    }

    .chart-h canvas {
        width: 100% !important;
        height: 100% !important;
        display: block;
    }

    .stock-item {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 10px;
    }

    .stock-item:last-child {
        margin-bottom: 0;
    }

    .stock-name {
        font-weight: 600;
        color: var(--ink);
    }

    .stock-qty {
        font-weight: 700;
        color: var(--danger);
    }

    @media (max-width: 768px) {
        .dashboard-shell {
            padding: 14px;
        }

        .hero {
            padding: 14px;
        }

        .stat-value {
            font-size: 1.35rem;
        }

        .chart-h {
            height: 240px;
        }
    }
</style>
@endsection

@section('content')
<div class="main-content">
    <div class="dashboard-v3 p-3">
    <div class="dashboard-shell">
        <div class="hero mb-3">
            <h3>Dashboard Tổng Quan</h3>
            <p>Theo dõi nhanh doanh thu, đơn hàng, khách hàng và hiệu suất bán sách.</p>
        </div>

        <div class="filter-card mb-3">
            <form action="{{ route('statistics.filter') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1">Từ ngày</label>
                    <input type="date" name="from" class="form-control" value="{{ request('from', isset($from) ? $from->toDateString() : '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Đến ngày</label>
                    <input type="date" name="to" class="form-control" value="{{ request('to', isset($to) ? $to->toDateString() : '') }}">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Lọc dữ liệu</button>
                </div>
                <div class="col-md-2 d-grid">
                    <a href="{{ route('admin') }}" class="btn btn-outline-secondary">Mặc định</a>
                </div>
                <div class="col-md-2 d-grid">
                    <a href="{{ route('statistics.export', ['from' => request('from'), 'to' => request('to')]) }}" class="btn btn-outline-success">Xuất Excel</a>
                </div>
            </form>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card stat-1">
                    <div class="stat-title">Tổng số sách</div>
                    <div class="stat-value">{{ number_format($overview['total_books']) }}</div>
                    <div class="stat-note">Sản phẩm đang quản lý</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card stat-2">
                    <div class="stat-title">Tổng đơn hàng</div>
                    <div class="stat-value">{{ number_format($overview['total_orders']) }}</div>
                    <div class="stat-note">Toàn bộ đơn trên hệ thống</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card stat-3">
                    <div class="stat-title">Tổng khách hàng</div>
                    <div class="stat-value">{{ number_format($overview['total_customers']) }}</div>
                    <div class="stat-note">Tài khoản thành viên</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card stat-4">
                    <div class="stat-title">Tổng doanh thu</div>
                    <div class="stat-value">{{ number_format($overview['total_revenue'], 0, ',', '.') }} đ</div>
                    <div class="stat-note">Đơn đã thanh toán thành công</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="stat-card stat-5">
                    <div class="stat-title">Doanh thu hôm nay</div>
                    <div class="stat-value">{{ number_format($overview['today_revenue'], 0, ',', '.') }} đ</div>
                    <div class="stat-note">Tính từ 00:00 đến hiện tại</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="stat-card stat-6">
                    <div class="stat-title">Đơn hàng hôm nay</div>
                    <div class="stat-value">{{ number_format($overview['today_orders']) }}</div>
                    <div class="stat-note">Số đơn phát sinh trong ngày</div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="stat-card stat-7">
                    <div class="stat-title">Sách sắp hết hàng</div>
                    <div class="stat-value">{{ number_format($lowStockBooks->count()) }}</div>
                    <div class="stat-note">Có số lượng <= 10</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-8">
                <div class="card chart-card">
                    <div class="card-header">Doanh thu theo ngày (khoảng lọc hiện tại)</div>
                    <div class="card-body chart-h">
                        <canvas id="revenueDayChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card chart-card h-100">
                    <div class="card-header">Biểu đồ trạng thái đơn hàng</div>
                    <div class="card-body chart-h">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="card chart-card">
                    <div class="card-header">Doanh thu theo tháng (12 tháng gần nhất)</div>
                    <div class="card-body chart-h">
                        <canvas id="revenueMonthChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card chart-card">
                    <div class="card-header">Doanh thu theo năm (5 năm)</div>
                    <div class="card-body chart-h">
                        <canvas id="revenueYearChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="card table-card">
                    <div class="card-header">Top sách bán chạy</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Sách</th>
                                        <th class="text-end">SL bán</th>
                                        <th class="text-end">Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topProducts as $product)
                                        <tr>
                                            <td>{{ $product->product_name }}</td>
                                            <td class="text-end">{{ number_format($product->total_quantity) }}</td>
                                            <td class="text-end">{{ number_format($product->total_revenue, 0, ',', '.') }} đ</td>
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

            <div class="col-lg-6">
                <div class="card table-card">
                    <div class="card-header">Top danh mục bán chạy</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Danh mục</th>
                                        <th class="text-end">SL bán</th>
                                        <th class="text-end">Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topCategories as $category)
                                        <tr>
                                            <td>{{ $category->name }}</td>
                                            <td class="text-end">{{ number_format($category->total_quantity) }}</td>
                                            <td class="text-end">{{ number_format($category->total_revenue, 0, ',', '.') }} đ</td>
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
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card chart-card">
                    <div class="card-header">Tăng trưởng khách hàng (12 tháng)</div>
                    <div class="card-body chart-h">
                        <canvas id="customerGrowthChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card stock-card h-100">
                    <div class="card-header">Sách sắp hết hàng</div>
                    <div class="card-body">
                        @forelse($lowStockBooks as $book)
                            <div class="stock-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stock-name">{{ $book->name }}</div>
                                    <small class="text-muted">{{ number_format($book->price, 0, ',', '.') }} đ</small>
                                </div>
                                <div class="stock-qty">{{ $book->quantity }}</div>
                            </div>
                        @empty
                            <div class="text-muted">Hiện không có sách sắp hết hàng.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@section('js')
@parent
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const fmtCurrency = (v) => new Intl.NumberFormat('vi-VN').format(v) + ' đ';

    const revenueByDay = {
        labels: @json($revenueByDay['labels']),
        values: @json($revenueByDay['values']),
    };

    const revenueByMonth = {
        labels: @json($revenueByMonth['labels']),
        values: @json($revenueByMonth['values']),
    };

    const revenueByYear = {
        labels: @json($revenueByYear['labels']),
        values: @json($revenueByYear['values']),
    };

    const orderStatus = {
        labels: @json($orderStatusStats['labels']),
        values: @json($orderStatusStats['values']),
    };

    const customerGrowth = {
        labels: @json($customerGrowth['labels']),
        newUsers: @json($customerGrowth['new_users']),
        cumulative: @json($customerGrowth['cumulative']),
    };

    new Chart(document.getElementById('revenueDayChart'), {
        type: 'line',
        data: {
            labels: revenueByDay.labels,
            datasets: [{
                label: 'Doanh thu',
                data: revenueByDay.values,
                borderColor: '#0ea5e9',
                backgroundColor: 'rgba(14,165,233,.16)',
                fill: true,
                tension: 0.35,
                pointRadius: 2.5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true },
                tooltip: {
                    callbacks: {
                        label: (ctx) => fmtCurrency(ctx.parsed.y || 0),
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: (value) => new Intl.NumberFormat('vi-VN').format(value),
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('orderStatusChart'), {
        type: 'doughnut',
        data: {
            labels: orderStatus.labels,
            datasets: [{
                data: orderStatus.values,
                backgroundColor: ['#f59e0b', '#10b981', '#0ea5e9', '#6366f1', '#ef4444'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    new Chart(document.getElementById('revenueMonthChart'), {
        type: 'bar',
        data: {
            labels: revenueByMonth.labels,
            datasets: [{
                label: 'Doanh thu theo tháng',
                data: revenueByMonth.values,
                backgroundColor: '#14b8a6',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (ctx) => fmtCurrency(ctx.parsed.y || 0),
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('revenueYearChart'), {
        type: 'bar',
        data: {
            labels: revenueByYear.labels,
            datasets: [{
                label: 'Doanh thu theo năm',
                data: revenueByYear.values,
                backgroundColor: '#3b82f6',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (ctx) => fmtCurrency(ctx.parsed.y || 0),
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('customerGrowthChart'), {
        data: {
            labels: customerGrowth.labels,
            datasets: [
                {
                    type: 'bar',
                    label: 'Khách hàng mới',
                    data: customerGrowth.newUsers,
                    backgroundColor: '#f59e0b',
                    yAxisID: 'y',
                    borderRadius: 5,
                },
                {
                    type: 'line',
                    label: 'Tổng khách hàng tích lũy',
                    data: customerGrowth.cumulative,
                    borderColor: '#0f766e',
                    backgroundColor: 'rgba(15, 118, 110, .12)',
                    yAxisID: 'y1',
                    tension: 0.35,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });
</script>
@endsection
