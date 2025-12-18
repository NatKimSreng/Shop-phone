@extends('layouts.app')
@section('title', 'Add -Category')
@section('content')
    <div class="card bg-dark-subtle">
        <div class="card-header">
            <h2>Create Category</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="category_name" class="form-label">Category Name</label>
                    <input type="text" name="category_name" id="category_name" class="form-control"
                        value="{{ old('category_name') }}" required maxlength="100">
                    @error('category_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Create Category</button>
            </form>
        </div>
    </div>
@endsection