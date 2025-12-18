@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 fw-bold">Dashboard</h2>
            <p class="text-muted small mb-0">Welcome back! Here's what's happening with your store.</p>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-1">Total Orders</div>
                            <div class="h4 mb-0 fw-bold">{{ number_format($totalOrders) }}</div>
                            <small class="text-muted">{{ $todayOrders }} today</small>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-1">Total Revenue</div>
                            <div class="h4 mb-0 fw-bold text-success">${{ number_format($totalRevenue, 2) }}</div>
                            <small class="text-muted">${{ number_format($todayRevenue, 2) }} today</small>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-1">Total Products</div>
                            <div class="h4 mb-0 fw-bold">{{ number_format($totalProducts) }}</div>
                            <small class="text-muted">Active products</small>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small mb-1">Total Customers</div>
                            <div class="h4 mb-0 fw-bold">{{ number_format($totalCustomers) }}</div>
                            <small class="text-muted">Registered users</small>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row mb-4">
        {{-- Revenue & Orders Chart --}}
        <div class="col-xl-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Revenue & Orders (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>

        {{-- Order Status Breakdown --}}
        <div class="col-xl-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Order Status</h5>
                </div>
                <div class="card-body">
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'paid' => 'success',
                            'processing' => 'info',
                            'shipped' => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger'
                        ];
                    @endphp
                    @foreach($orderStatuses as $status => $count)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }} me-2">
                                    {{ ucfirst($status) }}
                                </span>
                            </div>
                            <div class="font-weight-bold">{{ $count }}</div>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-{{ $statusColors[$status] ?? 'secondary' }}"
                                 role="progressbar"
                                 style="width: {{ $totalOrders > 0 ? ($count / $totalOrders * 100) : 0 }}%">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="row">
        {{-- Recent Orders --}}
        <div class="col-xl-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Recent Orders</h5>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none fw-bold text-primary">
                                                #{{ $order->order_number ?? $order->id }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $order->name }}</div>
                                            <small class="text-muted">{{ $order->email }}</small>
                                        </td>
                                        <td class="fw-bold text-success">${{ number_format($order->total, 2) }}</td>
                                        <td>
                                            @php
                                                $color = $statusColors[$order->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="fw-semibold">{{ $order->created_at->format('M d, Y') }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
                                            <div>No orders yet</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top Products & Payment Methods --}}
        <div class="col-xl-4 mb-4">
            {{-- Top Products --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Top Products</h5>
                </div>
                <div class="card-body">
                    @forelse($topProducts as $product)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="font-weight-bold">{{ $product->name }}</div>
                                <small class="text-muted">{{ $product->total_sold }} sold</small>
                            </div>
                            <div class="text-end">
                                <div class="font-weight-bold">${{ number_format($product->total_revenue, 2) }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No sales data</p>
                    @endforelse
                </div>
            </div>

            {{-- Payment Methods --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Payment Methods</h5>
                </div>
                <div class="card-body">
                    @foreach($paymentMethods as $method => $count)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-{{ $method == 'khqr' ? 'primary' : 'secondary' }}">
                                {{ strtoupper($method ?? 'COD') }}
                            </span>
                            <strong>{{ $count }} orders</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Revenue & Orders Line Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Revenue ($)',
                    data: @json($revenueData),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y',
                    fill: true
                },
                {
                    label: 'Orders',
                    data: @json($orderCountData),
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    enabled: true
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Orders'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
</script>
@endpush
@endsection

