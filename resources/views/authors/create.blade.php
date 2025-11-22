@extends('layouts.app')
@section('title', 'Create Author')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-1 font-weight-bold text-dark">Create Author</h2>
                    <p class="text-muted small mb-0">Add a new author</p>
                </div>
                <a href="{{ route('authors.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>

            <!-- Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('authors.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf

                        <!-- Name -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Name</label>
                            <input type="text" name="name" class="form-control form-control-lg" required>
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <!-- Website -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Website</label>
                            <input type="text" name="website" class="form-control">
                        </div>

                        <!-- Phone -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <!-- Save -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <a href="{{ route('authors.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-1"></i> Save Author
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
