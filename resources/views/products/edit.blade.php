@extends('layouts.app')
@section('title', 'Edit Product')
@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Edit Product</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data"
                    class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $product->name) }}" required maxlength="200">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" rows="5"
                            class="form-control @error('description') is-invalid @enderror" required maxlength="500">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Price ($)</label>
                        <input type="number" name="price" id="price"
                            class="form-control @error('price') is-invalid @enderror"
                            value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Current Image -->
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        @if ($product->image && file_exists(public_path($product->image)))
                            <div class="mb-2">
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="img-thumbnail"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                                <small class="text-muted d-block">Current image. Upload new to replace.</small>
                            </div>
                        @else
                            <small class="text-muted d-block mb-2">No image uploaded yet.</small>
                        @endif
                        <input type="file" name="image" id="image"
                            class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
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
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stock -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="stock" id="stock" class="form-check-input" value="1"
                            {{ old('stock', $product->stock) ? 'checked' : '' }}>
                        <label for="stock" class="form-check-label">In Stock</label>
                    </div>

                    <!-- Buttons -->
                    <div class="mb-3 d-flex justify-content-end">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm mr-3">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm">Update </button>
                    </div>
                </form>

                <!-- Delete Product -->
                {{-- <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this product?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">Delete Product</button>
                </form> --}}
            </div>
        </div>
    </div>
@endsection
