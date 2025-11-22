@extends('layouts.app')
@section('title', $product->name)
@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h2 class="mb-0 font-weight-bold text-dark">{{ $product->name }}</h2>
                <div>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-4">
                        @if ($product->image)
                            <div class="product-image-wrapper">
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" 
                                    class="img-fluid rounded shadow product-image">
                            </div>
                        @else
                            <div class="bg-light text-center p-5 rounded shadow-sm border">
                                <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No Image Available</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <div class="product-details">
                            <div class="mb-4">
                                <label class="text-muted small mb-1 d-block">DESCRIPTION</label>
                                <p class="mb-0 text-dark" style="line-height: 1.6;">{{ $product->description }}</p>
                            </div>

                            <div class="mb-4">
                                <label class="text-muted small mb-1 d-block">PRICE</label>
                                <h3 class="mb-0 text-success font-weight-bold">${{ number_format($product->price, 2) }}</h3>
                            </div>

                            <div class="mb-4">
                                <label class="text-muted small mb-1 d-block">CATEGORY</label>
                                @if ($product->category)
                                    <span class="badge bg-primary px-3 py-2" style="font-size: 0.9rem;">
                                        <i class="fas fa-tag me-1"></i>{{ $product->category->category_name }}
                                    </span>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-question-circle me-1"></i>Uncategorized
                                    </span>
                                @endif
                            </div>

                            <div class="mb-4">
                                <label class="text-muted small mb-1 d-block">STOCK STATUS</label>
                                <span class="badge {{ $product->stock ? 'bg-success' : 'bg-danger' }} px-3 py-2" 
                                      style="font-size: 0.9rem;">
                                    <i class="fas {{ $product->stock ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                    {{ $product->stock ? 'In Stock' : 'Out of Stock' }}
                                </span>
                            </div>

                            <div class="row g-3 mt-3 pt-3 border-top">
                                <div class="col-sm-6">
                                    <label class="text-muted small mb-1 d-block">CREATED</label>
                                    <p class="mb-0 text-dark">
                                        <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                        {{ $product->created_at->format('M d, Y H:i') }}
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <label class="text-muted small mb-1 d-block">UPDATED</label>
                                    <p class="mb-0 text-dark">
                                        <i class="fas fa-clock me-1 text-muted"></i>
                                        {{ $product->updated_at->format('M d, Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .product-image-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
        }
        .product-image {
            max-height: 400px;
            width: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
            border: 2px solid #e9ecef;
        }
        .product-image:hover {
            transform: scale(1.05);
        }
        .product-details label {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.75rem;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transition: all 0.2s ease;
        }
        .card {
            border-radius: 10px;
        }
        .badge {
            transition: transform 0.2s ease;
        }
        .badge:hover {
            transform: scale(1.05);
        }
    </style>
@endsection
