@extends('layouts.admin')

@section('title', 'Edit Teacher Subject Assignment')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.teacher-subject-assignments.index') }}">Teacher Subject Assignments</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Teacher Subject Assignment</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.teacher-subject-assignments.update', $assignment->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="teacher_id" class="form-label">Teacher</label>
                        <select class="form-select @error('teacher_id') is-invalid @enderror" id="teacher_id" name="teacher_id" required>
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $id => $name)
                                <option value="{{ $id }}" {{ old('teacher_id', $assignment->teacher_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $id => $name)
                                <option value="{{ $id }}" {{ old('subject_id', $assignment->subject_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="class_arm_id" class="form-label">Class Arm</label>
                        <select class="form-select @error('class_arm_id') is-invalid @enderror" id="class_arm_id" name="class_arm_id" required>
                            <option value="">Select Class Arm</option>
                            @foreach($classArms as $className => $arms)
                                <optgroup label="{{ $className }}">
                                    @foreach($arms as $arm)
                                        <option value="{{ $arm->id }}" {{ old('class_arm_id', $assignment->class_arm_id) == $arm->id ? 'selected' : '' }}>{{ $arm->arm_name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('class_arm_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="academic_session_id" class="form-label">Academic Session</label>
                        <select class="form-select @error('academic_session_id') is-invalid @enderror" id="academic_session_id" name="academic_session_id" required>
                            <option value="">Select Session</option>
                            @foreach($sessions as $id => $name)
                                <option value="{{ $id }}" {{ old('academic_session_id', $assignment->academic_session_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('academic_session_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $assignment->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $assignment->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.teacher-subject-assignments.index') }}" class="btn btn-secondary">
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
