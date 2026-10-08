@extends('layouts.admin')

@section('title', 'Edit Class')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.classes.index') }}">Classes</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Class</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.classes.update', $class->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Class Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $class->name) }}" placeholder="e.g., JSS 1" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">Class Code</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $class->code) }}" placeholder="e.g., JSS1" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="section" class="form-label">Section</label>
                        <select class="form-select @error('section') is-invalid @enderror" id="section" name="section" required>
                            <option value="">Select Section</option>
                            <option value="nursery" {{ old('section', $class->section) == 'nursery' ? 'selected' : '' }}>Nursery</option>
                            <option value="primary" {{ old('section', $class->section) == 'primary' ? 'selected' : '' }}>Primary</option>
                            <option value="junior_secondary" {{ old('section', $class->section) == 'junior_secondary' ? 'selected' : '' }}>Junior Secondary</option>
                            <option value="senior_secondary" {{ old('section', $class->section) == 'senior_secondary' ? 'selected' : '' }}>Senior Secondary</option>
                        </select>
                        @error('section')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="display_order" name="display_order" value="{{ old('display_order', $class->display_order) }}" min="0">
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $class->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $class->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
