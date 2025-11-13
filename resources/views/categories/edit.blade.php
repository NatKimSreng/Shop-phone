@extends('layouts.app')
@section('title', 'Edit Category: ' . $category->category_name)
@section('content')
    <div class="card bg-dark-subtle">
        <div class="card-header">
            <h2>Edit Category</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('categories.update', $category) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="category_name" class="form-label">Category Name</label>
                    <input type="text" name="category_name" id="category_name"
                        class="form-control @error('category_name') is-invalid @enderror"
                        value="{{ old('category_name', $category->category_name) }}" required maxlength="200">
                    @error('category_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category_description" class="form-label">Description</label>
                    <textarea name="category_description" id="category_description"
                        class="form-control @error('category_description') is-invalid @enderror" rows="4" maxlength="1000">
                        {{ old('category_description', $category->category_description) }}
                    </textarea>
                    @error('category_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
@endsection
