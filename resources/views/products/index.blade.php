@extends('layouts.app')
@section('title', 'Products')
@section('content')
    <div class="container-fluid px-4 py-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 font-weight-bold">Products</h1>
                <p class="text-muted small mb-0">Manage your product inventory</p>
            </div>
            <a href="{{ route('products.create') }}" class="btn btn-dark btn-sm px-4">
                <i class="fas fa-plus mr-1"></i> Add Product
            </a>
        </div>

        <!-- Filters Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-2">Category</label>
                            <select name="category_id" class="form-control form-control-sm border-secondary">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name ?? $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-2">Stock Status</label>
                            <select name="stock" class="form-control form-control-sm border-secondary">
                                <option value="">All</option>
                                <option value="1" {{ request('stock') === '1' ? 'selected' : '' }}>In Stock</option>
                                <option value="0" {{ request('stock') === '0' ? 'selected' : '' }}>Out of Stock
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small text-muted mb-2">Min Price</label>
                            <input type="number" name="price" class="form-control form-control-sm border-secondary"
                                placeholder="e.g., 100" step="0.01" min="0" value="{{ request('price') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-2">Search</label>
                            <input type="text" name="search" class="form-control form-control-sm border-secondary"
                                placeholder="Search by name or description..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-dark btn-sm w-100" type="submit">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            @if (request()->hasAny(['category_id', 'stock', 'search', 'price']))
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Grid/Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-normal">PRODUCT</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-normal">DESCRIPTION</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-normal text-right">PRICE</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-normal text-center">STATUS</th>
                                <th class="border-0 px-4 py-3 text-muted small font-weight-normal text-center">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($products))
                                @forelse ($products as $product)
                                    <tr data-id="{{ $product->id }}" class="align-middle">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                @if ($product->image)
                                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                                                        class="rounded"
                                                        style="width: 45px; height: 45px; object-fit: cover;">
                                                @else
                                                    <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                        style="width: 45px; height: 45px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div class="ml-3">
                                                    <a href="{{ route('products.show', $product) }}"
                                                        class="text-dark font-weight-500 text-decoration-none">
                                                        {{ $product->name }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-muted small" style="max-width: 250px;">
                                            {{ Str::limit($product->description, 60) }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-weight-500">
                                            ${{ number_format($product->price, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button
                                                class="btn btn-sm toggle-stock border-0 px-3 py-1 {{ $product->stock ? 'bg-success text-white' : 'bg-secondary text-white' }}"
                                                style="border-radius: 20px; font-size: 0.75rem;">
                                                {{ $product->stock ? 'In Stock' : 'Out of Stock' }}
                                            </button>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('products.edit', $product) }}"
                                                    class="btn btn-outline-secondary border" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('products.destroy', $product) }}"
                                                    class="d-inline" onsubmit="return confirm('Delete this product?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger border" type="submit"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-0">No products found</p>
                                            <a href="{{ route('products.create') }}" class="btn btn-dark btn-sm mt-3">Add
                                                Your First Product</a>
                                        </td>
                                    </tr>
                                @endforelse
                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                        <p class="text-warning mb-0">Products data not loaded (controller issue). Refresh
                                            or check logs.</p>
                                        <a href="{{ route('products.index') }}"
                                            class="btn btn-primary btn-sm mt-3">Reload</a>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if (isset($products) && $products->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endsection

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

