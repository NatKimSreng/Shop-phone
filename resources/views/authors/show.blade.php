@extends('layouts.app')
@section('title', 'Author Detail')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1 font-weight-bold text-dark">Author Detail</h2>
                    <p class="text-muted small mb-0">Detailed information</p>
                </div>
                <a href="{{ route('authors.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <p><strong>Name:</strong> {{ $author->name }}</p>
                    <p><strong>Email:</strong> {{ $author->email }}</p>
                    <p><strong>Website:</strong> {{ $author->website }}</p>
                    <p><strong>Phone:</strong> {{ $author->phone }}</p>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
