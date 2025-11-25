@extends('admin.layouts.admin')

@section('title', 'Thống kê')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="mb-4">
            <h1 class="page-title mb-1">📊 Bảng Thống Kê</h1>
            <p class="page-subtitle">Quản lý và phân tích dữ liệu kinh doanh của bạn</p>
        </div>

        <!-- Filter Section -->
        @php
            use Carbon\Carbon;
            $currentMonth = Carbon::now();
            $months = [];

            // Tạo danh sách 6 tháng (hiện tại và 5 tháng trước)
            for ($i = 0; $i < 6; $i++) {
                $monthDate = $currentMonth->copy()->subMonths($i);
                $months[] = [
                    'value' => $monthDate->format('Y-m'),
                    'label' => 'Tháng ' . $monthDate->format('m/Y'),
                ];
            }

            // Giá trị đang chọn
            $selectedMonth = $month ?? Carbon::now()->format('Y-m');
        @endphp

        <div class="filter-section">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <form method="GET">
                        <label class="form-label"><i class="fas fa-calendar"></i> Chọn tháng thống kê</label>
                        <select name="month" class="form-select" onchange="this.form.submit()">
                            @foreach ($months as $m)
                                <option value="{{ $m['value'] }}" {{ $selectedMonth == $m['value'] ? 'selected' : '' }}>
                                    {{ $m['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid mb-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-card-title"><i class="fas fa-shopping-cart"></i> Tổng Đơn Hàng</p>
                        <h2 class="stat-card-value">{{ $totalOrders }}</h2>
                        <p class="stat-card-change positive"><i class="fas fa-arrow-up"></i> Tháng này</p>
                    </div>
                    <i class="fas fa-chart-line" style="font-size: 2.5rem; color: #667eea; opacity: 0.2;"></i>
                </div>
            </div>

            <div class="stat-card" style="border-left-color: #f5576c;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-card-title"><i class="fas fa-money-bill-wave"></i> Tổng Doanh Thu</p>
                        <h2 class="stat-card-value">{{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                        <p class="stat-card-change positive"><i class="fas fa-arrow-up"></i> ₫</p>
                    </div>
                    <i class="fas fa-chart-bar" style="font-size: 2.5rem; color: #f5576c; opacity: 0.2;"></i>
                </div>
            </div>

            <div class="stat-card" style="border-left-color: #11998e;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-card-title"><i class="fas fa-box"></i> Doanh Thu Trung Bình</p>
                        <h2 class="stat-card-value">{{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 0, ',', '.') : 0 }}</h2>
                        <p class="stat-card-change positive"><i class="fas fa-arrow-up"></i> Mỗi đơn</p>
                    </div>
                    <i class="fas fa-chart-pie" style="font-size: 2.5rem; color: #11998e; opacity: 0.2;"></i>
                </div>
            </div>
        </div>

        <!-- Daily Revenue Section -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-chart-area me-2"></i>Doanh Thu Theo Ngày
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-calendar-day"></i> Ngày</th>
                                        <th><i class="fas fa-money-bill"></i> Doanh Thu</th>
                                        <th><i class="fas fa-percent"></i> Tỷ Lệ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalRevenueTemp = $totalRevenue > 0 ? $totalRevenue : 1; @endphp
                                    @forelse($dailyRevenue as $d)
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <i class="fas fa-calendar"></i> Ngày {{ str_pad($d->day, 2, '0', STR_PAD_LEFT) }}
                                                </span>
                                            </td>
                                            <td><strong>{{ number_format($d->revenue, 0, ',', '.') }} ₫</strong></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: {{ ($d->revenue / $totalRevenueTemp * 100) }}%">
                                                        {{ round(($d->revenue / $totalRevenueTemp * 100), 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox" style="font-size: 2rem;"></i><br>
                                                Không có dữ liệu doanh thu
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-list-ul me-2"></i>Tóm Tắt Tháng
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">Tổng Đơn Hàng</small>
                            <h5 class="mb-0">{{ $totalOrders }} <span class="small text-muted">đơn</span></h5>
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 100%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Doanh Thu</small>
                            <h5 class="mb-0">{{ number_format($totalRevenue, 0, ',', '.') }} ₫</h5>
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar" style="background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); width: 100%"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Ngày Có Dữ Liệu</small>
                            <h5 class="mb-0">{{ count($dailyRevenue) }} <span class="small text-muted">ngày</span></h5>
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); width: 100%"></div>
                            </div>
                        </div>

                        <hr>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle"></i> Dữ liệu được cập nhật tự động
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-fire me-2" style="color: #ff6b6b;"></i>Top 10 Sản Phẩm Bán Chạy
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-hashtag"></i> Xếp Hạng</th>
                                        <th><i class="fas fa-box"></i> Sản Phẩm</th>
                                        <th><i class="fas fa-barcode"></i> Thương Hiệu</th>
                                        <th><i class="fas fa-tags"></i> Thuộc Tính</th>
                                        <th><i class="fas fa-chart-line"></i> Số Lượng Bán</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $rank = 1; @endphp
                                    @forelse($topProducts as $p)
                                        @if ($p->order_items_sum_quantity > 0)
                                            <tr>
                                                <td>
                                                    @if($rank == 1)
                                                        <span class="badge" style="background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%); color: #333;">🏆 #{{ $rank }}</span>
                                                    @elseif($rank == 2)
                                                        <span class="badge" style="background: linear-gradient(135deg, #c0c0c0 0%, #808080 100%); color: #fff;">🥈 #{{ $rank }}</span>
                                                    @elseif($rank == 3)
                                                        <span class="badge" style="background: linear-gradient(135deg, #cd7f32 0%, #8b4513 100%); color: #fff;">🥉 #{{ $rank }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">#{{ $rank }}</span>
                                                    @endif
                                                </td>
                                                <td><strong>{{ $p->product->name }}</strong></td>
                                                <td>{{ $p->brand }}</td>
                                                <td><span class="badge bg-info">{{ $p->attribute }}</span></td>
                                                <td>
                                                    <h6 class="mb-0">
                                                        <span class="badge bg-success">{{ $p->order_items_sum_quantity }} sản phẩm</span>
                                                    </h6>
                                                </td>
                                            </tr>
                                            @php $rank++; @endphp
                                        @endif
                                        @if($rank > 10) @break @endif
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox" style="font-size: 2rem;"></i><br>
                                                Không có sản phẩm nào được bán
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
