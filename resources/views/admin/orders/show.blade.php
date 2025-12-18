@extends('layouts.app')

@section('title', 'Order Details #' . ($order->order_number ?? $order->id))

@section('content')
<div class="container-fluid px-4 py-4">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-1 fw-bold">Order #{{ $order->order_number ?? $order->id }}</h2>
            <p class="text-muted small mb-0">
                Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Main Content --}}
        <div class="col-lg-8">
            
            {{-- Order Items --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ asset($item->product->image) }}" 
                                                         alt="{{ $item->product->name }}"
                                                         class="me-3" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                @endif
                                                <div>
                                                    <div class="font-weight-bold">
                                                        {{ $item->product->name ?? 'Deleted Product #' . $item->product_id }}
                                                    </div>
                                                    @if($item->product && $item->product->category)
                                                        <small class="text-muted">{{ $item->product->category->category_name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $item->qty }}</span>
                                        </td>
                                        <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">
                                            <strong>${{ number_format($item->subtotal ?? ($item->price * $item->qty), 2) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Order Notes --}}
            @if($order->notes)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 fw-bold">Order Notes</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            
            {{-- Order Status --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Order Status</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Status</label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </form>

                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'paid' => 'success',
                            'processing' => 'info',
                            'shipped' => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $color = $statusColors[$order->status] ?? 'secondary';
                    @endphp
                    <div class="text-center">
                        <span class="badge bg-{{ $color }} fs-6 px-3 py-2">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Customer Information --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Name:</strong><br>
                        {{ $order->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <a href="mailto:{{ $order->email }}">{{ $order->email }}</a>
                    </div>
                    <div class="mb-3">
                        <strong>Phone:</strong><br>
                        <a href="tel:{{ $order->phone }}">{{ $order->phone }}</a>
                    </div>
                    @if($order->user)
                        <div class="mb-3">
                            <strong>User Account:</strong><br>
                            {{ $order->user->name }} (ID: {{ $order->user->id }})
                        </div>
                    @else
                        <div class="mb-3">
                            <span class="badge bg-secondary">Guest Order</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Shipping Address</h5>
                </div>
                <div class="card-body">
                    <address class="mb-0">
                        {{ $order->name }}<br>
                        {{ $order->address }}<br>
                        @if($order->city)
                            {{ $order->city }}{{ $order->postal_code ? ', ' . $order->postal_code : '' }}<br>
                        @endif
                        @if($order->country)
                            {{ $order->country }}
                        @endif
                    </address>
                </div>
            </div>

            {{-- Payment Information --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Payment Method:</strong><br>
                        <span class="badge bg-{{ $order->payment_method == 'khqr' ? 'primary' : 'secondary' }}">
                            {{ strtoupper($order->payment_method ?? 'COD') }}
                        </span>
                    </div>
                    @if($order->paid_at)
                        <div class="mb-3">
                            <strong>Paid At:</strong><br>
                            {{ \Carbon\Carbon::parse($order->paid_at)->format('F d, Y \a\t g:i A') }}
                        </div>
                    @endif
                    @if($order->khqr_md5)
                        <div class="mb-3">
                            <strong>KHQR Transaction ID:</strong><br>
                            <code class="small">{{ $order->khqr_md5 }}</code>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong>${{ number_format($order->subtotal ?? $order->total, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span class="text-success">Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="fs-5">${{ number_format($order->total, 2) }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Items Count:</span>
                        <strong>{{ $order->orderItems->count() }} item(s)</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Order Date:</span>
                        <strong>{{ $order->created_at->format('M d, Y H:i') }}</strong>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card mt-4">
                <div class="card-body">
                    <form action="{{ route('admin.orders.destroy', $order) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash me-1"></i> Delete Order
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

@push('scripts')
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
        </script>
    @endif
@endpush
@endsection

