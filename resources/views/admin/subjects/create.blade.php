@extends('layouts.admin')

@section('title', 'Create Subject')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.subjects.index') }}">Subjects</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Create Subject</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subjects.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Subject Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., Mathematics" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">Subject Code</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="e.g., MATH" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="core" {{ old('category') == 'core' ? 'selected' : '' }}>Core</option>
                            <option value="elective" {{ old('category') == 'elective' ? 'selected' : '' }}>Elective</option>
                            <option value="science" {{ old('category') == 'science' ? 'selected' : '' }}>Science</option>
                            <option value="arts" {{ old('category') == 'arts' ? 'selected' : '' }}>Arts</option>
                            <option value="commercial" {{ old('category') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>Technical</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i>Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
