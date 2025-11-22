@extends('layouts.app')
@section('title', 'Edit Product')
@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-1 font-weight-bold text-dark">Edit Product</h2>
                        <p class="text-muted small mb-0">Update product information</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <!-- Form Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data"
                            class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')

                            <!-- Name -->
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold text-dark mb-2">
                                    <i class="fas fa-tag me-1 text-muted"></i>Product Name
                                </label>
                                <input type="text" name="name" id="name"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror"
                                    value="{{ old('name', $product->name) }}" required maxlength="200">
                                @error('name')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="description" class="form-label fw-semibold text-dark mb-2">
                                    <i class="fas fa-align-left me-1 text-muted"></i>Description
                                </label>
                                <textarea name="description" id="description" rows="4"
                                    class="form-control @error('description') is-invalid @enderror" required maxlength="500">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Price and Category Row -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="price" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-dollar-sign me-1 text-muted"></i>Price ($)
                                    </label>
                                    <input type="number" name="price" id="price"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                                    @error('price')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="category_id" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-folder me-1 text-muted"></i>Category
                                    </label>
                                    <select name="category_id" id="category_id"
                                        class="form-select @error('category_id') is-invalid @enderror" required>
                                        <option value="">Select a Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Current Image -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark mb-2">
                                    <i class="fas fa-image me-1 text-muted"></i>Product Image
                                </label>
                                @if ($product->image && file_exists(public_path($product->image)))
                                    <div class="mb-3 p-3 bg-light rounded border">
                                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" 
                                            class="img-thumbnail mb-2 current-product-image"
                                            style="width: 150px; height: 150px; object-fit: cover; border: 2px solid #dee2e6;">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-info-circle me-1"></i>Current image. Upload new to replace.
                                        </small>
                                    </div>
                                @else
                                    <div class="mb-3 p-3 bg-light rounded border text-center">
                                        <i class="fas fa-image fa-2x text-muted mb-2 d-block"></i>
                                        <small class="text-muted">No image uploaded yet.</small>
                                    </div>
                                @endif
                                <input type="file" name="image" id="image"
                                    class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Stock -->
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="stock" id="stock" class="form-check-input" 
                                        value="1" role="switch" {{ old('stock', $product->stock) ? 'checked' : '' }}>
                                    <label for="stock" class="form-check-label fw-semibold text-dark ms-2">
                                        <i class="fas fa-boxes me-1 text-muted"></i>In Stock
                                    </label>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-1"></i> Update Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
            transition: all 0.2s ease;
        }
        .form-control-lg {
            font-size: 1rem;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transition: all 0.2s ease;
        }
        .card {
            border-radius: 10px;
        }
        .form-label {
            font-size: 0.9rem;
        }
        .current-product-image {
            transition: transform 0.3s ease;
        }
        .current-product-image:hover {
            transform: scale(1.05);
        }
        .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }
        .form-check-input {
            width: 3rem;
            height: 1.5rem;
            cursor: pointer;
        }
        input[type="file"] {
            cursor: pointer;
        }
    </style>
@endsection
