@extends('layouts.app')
@section('title', 'Home - Category List')
@section('content')
    <form method="GET" action="{{ route('categories.index') }}" class="form-inline mb-3">
        <label class="mr-2">Search</label>
        <input type="text" name="search" class="form-control mr-2" placeholder="Search name or description"
            value="{{ request('search') }}">

        <button class="btn btn-secondary btn-sm">Filter</button>
        <a href="{{ route('categories.index') }}" class="btn btn-light ml-2">Reset</a>
    </form>

    <div class="table-responsive">
        <div class="d-flex align-items-center justify-content-end my-2">
            <a href="{{ route('categories.create') }}" class="btn btn-success btn-sm ml-2">Add New</a>
        </div>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th class="text-center w-auto">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr data-id="{{ $category->id }}">
                        <td><a href="{{ route('categories.show', $category) }}">{{ $category->category_name }}</a></td>
                        <td class="text-center w-auto">
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}" class="d-inline"
                                onsubmit="return confirm('Delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination (if implemented in controller) -->
        @if(isset($categories) && method_exists($categories, 'links'))
            <div class="mt-3">
                {{ $categories->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endsection
