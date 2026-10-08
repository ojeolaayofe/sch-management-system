@extends('layouts.admin')

@section('title', 'Edit Class Arm')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.class-arms.index') }}">Class Arms</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Class Arm</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.class-arms.update', $arm->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Class</label>
                        <select class="form-select @error('class_id') is-invalid @enderror" id="class_id" name="class_id" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $id => $name)
                                <option value="{{ $id }}" {{ old('class_id', $arm->class_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="arm_name" class="form-label">Arm Name</label>
                        <input type="text" class="form-control @error('arm_name') is-invalid @enderror" id="arm_name" name="arm_name" value="{{ old('arm_name', $arm->arm_name) }}" placeholder="e.g., A, B, C" required>
                        @error('arm_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', $arm->capacity) }}" min="1" required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="class_teacher_id" class="form-label">Class Teacher</label>
                        <select class="form-select" id="class_teacher_id" name="class_teacher_id">
                            <option value="">Select Teacher (Optional)</option>
                            @foreach($teachers as $id => $name)
                                <option value="{{ $id }}" {{ old('class_teacher_id', $arm->class_teacher_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $arm->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $arm->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.class-arms.index') }}" class="btn btn-secondary">
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
