@extends('admin.layouts.admin')

@section('title', 'Thống kê')

@section('content')
    <div class="container mt-4">
        <h2>Thống kê tháng {{ $month }}</h2>

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

        <form method="GET" class="mb-3">
            <label class="form-label">Chọn tháng thống kê:</label>
            <select name="month" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
                @foreach ($months as $m)
                    <option value="{{ $m['value'] }}" {{ $selectedMonth == $m['value'] ? 'selected' : '' }}>
                        {{ $m['label'] }}
                    </option>
                @endforeach
            </select>
        </form>


        <div class="row text-center mb-4">
            <div class="col-md-6">
                <div class="card p-3 shadow">
                    <h5>Tổng đơn hàng</h5>
                    <h3>{{ $totalOrders }}</h3>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3 shadow">
                    <h5>Tổng doanh thu</h5>
                    <h3>{{ number_format($totalRevenue, 0, ',', '.') }} ₫</h3>
                </div>
            </div>
        </div>

        <h4>Doanh thu theo ngày</h4>
        <ul>
            @foreach($dailyRevenue as $d)
                <li>Ngày {{ $d->day }}: {{ number_format($d->revenue, 0, ',', '.') }} ₫</li>
            @endforeach
        </ul>

        <h4>Top 5 sản phẩm bán chạy</ <!-- <ul>h4>
            @foreach($topProducts as $p)
            <li>{{ $p->name }} - {{ $p->total_sold }} sản phẩm</li>
            @endforeach
            </ul> -->
    </div>
@endsection