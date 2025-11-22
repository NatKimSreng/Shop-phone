@extends('layouts.app')
@section('title', 'Create Product')

@section('content')
    <div class="container-fluid px-4 py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h3 mb-1 font-weight-bold text-dark">Create Product</h2>
                        <p class="text-muted small mb-0">Add a new product to your inventory</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <!-- Form Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Product Name -->
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold text-dark mb-2">
                                    <i class="fas fa-tag me-1 text-muted"></i>Product Name
                                </label>
                                <input type="text" name="name" id="name" class="form-control form-control-lg"
                                    value="{{ old('name') }}" placeholder="Enter product name" required>
                                @error('name')
                                    <small class="text-danger d-block mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <label for="description" class="form-label fw-semibold text-dark mb-2">
                                    <i class="fas fa-align-left me-1 text-muted"></i>Description
                                </label>
                                <textarea name="description" id="description" rows="4" class="form-control"
                                    placeholder="Enter product description" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <small class="text-danger d-block mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <!-- Price and Category Row -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="price" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-dollar-sign me-1 text-muted"></i>Price ($)
                                    </label>
                                    <input type="number" step="0.01" name="price" id="price" class="form-control"
                                        value="{{ old('price') }}" placeholder="0.00" required>
                                    @error('price')
                                        <small class="text-danger d-block mt-1">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="category_id" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-folder me-1 text-muted"></i>Category
                                    </label>
                                    <select name="category_id" id="category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <small class="text-danger d-block mt-1">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Stock and Image Row -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="stock" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-boxes me-1 text-muted"></i>Stock Status
                                    </label>
                                    <select name="stock" id="stock" class="form-select">
                                        <option value="1" {{ old('stock', 1) ? 'selected' : '' }}>In Stock</option>
                                        <option value="0">Out of Stock</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="image" class="form-label fw-semibold text-dark mb-2">
                                        <i class="fas fa-image me-1 text-muted"></i>Product Image
                                    </label>
                                    <input type="file" name="image" id="image" class="form-control"
                                        accept="image/png,image/jpeg">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Accepted: PNG/JPG, Max size 2MB
                                    </small>
                                    @error('image')
                                        <small class="text-danger d-block mt-1">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-1"></i> Save Product
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
        input[type="file"] {
            cursor: pointer;
        }
    </style>
@endsection
