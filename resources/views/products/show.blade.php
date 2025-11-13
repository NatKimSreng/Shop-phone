@extends('layouts.app')
@section('title', $product->name)
@section('content')
    <div class="container">
        <div class="card bg-dark-subtle">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>{{ $product->name }}</h2>
                <div>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if ($product->image)
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded"
                                style="max-height: 300px; object-fit: cover;">
                        @else
                            <div class="bg-light text-center p-5 rounded">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="text-muted mt-2">No Image Available</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <dl class="row">
                            <dt class="col-sm-3">Description:</dt>
                            <dd class="col-sm-9">{{ $product->description }}</dd>

                            <dt class="col-sm-3">Price:</dt>
                            <dd class="col-sm-9">${{ number_format($product->price, 2) }}</dd>

                            <dt class="col-sm-3">Category:</dt>
                            <dd class="col-sm-9">
                                @if ($product->category)
                                    <span class="badge bg-primary">{{ $product->category->category_name }}</span>
                                @else
                                    <span class="text-muted">Uncategorized</span>
                                @endif
                            </dd>

                            <dt class="col-sm-3">Stock Status:</dt>
                            <dd class="col-sm-9">
                                <span class="badge {{ $product->stock ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->stock ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </dd>

                            <dt class="col-sm-3">Created:</dt>
                            <dd class="col-sm-9">{{ $product->created_at->format('M d, Y H:i') }}</dd>

                            <dt class="col-sm-3">Updated:</dt>
                            <dd class="col-sm-9">{{ $product->updated_at->format('M d, Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
