@extends('layouts.admin')

@section('title', 'Edit Academic Term')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.academic-terms.index') }}">Academic Terms</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Academic Term</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.academic-terms.update', $term->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="academic_session_id" class="form-label">Academic Session</label>
                        <select class="form-select @error('academic_session_id') is-invalid @enderror" id="academic_session_id" name="academic_session_id" required>
                            <option value="">Select Session</option>
                            @foreach($sessions as $id => $name)
                                <option value="{{ $id }}" {{ old('academic_session_id', $term->academic_session_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('academic_session_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Term Name</label>
                        <select class="form-select @error('name') is-invalid @enderror" id="name" name="name" required>
                            <option value="">Select Term</option>
                            <option value="first_term" {{ old('name', $term->name) == 'first_term' ? 'selected' : '' }}>First Term</option>
                            <option value="second_term" {{ old('name', $term->name) == 'second_term' ? 'selected' : '' }}>Second Term</option>
                            <option value="third_term" {{ old('name', $term->name) == 'third_term' ? 'selected' : '' }}>Third Term</option>
                            <option value="annual" {{ old('name', $term->name) == 'annual' ? 'selected' : '' }}>Annual</option>
                        </select>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $term->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $term->end_date->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_current" id="is_current" value="1" {{ old('is_current', $term->is_current) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_current">
                                Set as Current Term
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $term->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $term->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.academic-terms.index') }}" class="btn btn-secondary">
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
