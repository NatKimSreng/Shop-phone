@extends('layouts.app')
@section('title', $category->category_name)
@section('content')
    <div class="container">
        <div class="card bg-dark-subtle">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>{{ $category->category_name }}</h2>
                <div>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning btn-sm">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Name:</dt>
                    <dd class="col-sm-9"><strong>{{ $category->category_name }}</strong></dd>

                    <dt class="col-sm-3">Description:</dt>
                    <dd class="col-sm-9">{{ $category->category_description ?? 'No description' }}</dd>

                    <dt class="col-sm-3">Created:</dt>
                    <dd class="col-sm-9">{{ $category->created_at->format('M d, Y H:i') }}</dd>

                    <dt class="col-sm-3">Updated:</dt>
                    <dd class="col-sm-9">{{ $category->updated_at->format('M d, Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
@endsection
