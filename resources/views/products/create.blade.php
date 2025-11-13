@extends('layouts.app')
@section('title', 'Create Product')

@section('content')
    <!-- Bootstrap CDN -->

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <h2 class="text-center mb-4 text-primary">Create Product</h2>

                <div class="border rounded p-4 bg-light">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Product Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Product Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}" placeholder="Enter product name" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control"
                                placeholder="Enter product description" required>{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div class="mb-3">
                            <label for="price" class="form-label fw-semibold">Price ($)</label>
                            <input type="number" step="0.01" name="price" id="price" class="form-control"
                                value="{{ old('price') }}" placeholder="0.00" required>
                            @error('price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-semibold mr-4">Category</label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="">Select Category </option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Stock -->
                        <div class="mb-3">
                            <label for="stock" class="form-label fw-semibold">Stock Status</label>
                            <select name="stock" id="stock" class="form-select">
                                <option value="1" {{ old('stock', 1) ? 'selected' : '' }}>In Stock</option>
                                <option value="0">Out of Stock</option>
                            </select>
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label fw-semibold">Product Image</label>
                            <input type="file" name="image" id="image" class="form-control"
                                accept="image/png,image/jpeg">
                            <small class="text-muted">Accepted: PNG/JPG, Max size 2MB</small><br>
                            @error('image')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" class="btn btn-primary">Save Product</button>
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
@endsection
