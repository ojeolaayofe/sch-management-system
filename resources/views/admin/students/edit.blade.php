@extends('layouts.admin')

@section('title', 'Edit Student')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
<li class="breadcrumb-item active">Edit - {{ $student->student_id }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Edit Student</h4>
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Student ID</label>
                    <input type="text" name="student_id" class="form-control" value="{{ $student->student_id }}" readonly>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $student->first_name) }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror" value="{{ old('middle_name', $student->middle_name) }}">
                    @error('middle_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $student->last_name) }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}">
                    @error('date_of_birth')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                    <input type="date" name="admission_date" class="form-control @error('admission_date') is-invalid @enderror" value="{{ old('admission_date', $student->admission_date->format('Y-m-d')) }}" required>
                    @error('admission_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $student->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $student->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="transferred" {{ old('status', $student->status) == 'transferred' ? 'selected' : '' }}>Transferred</option>
                        <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $student->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-12"><hr></div>
                
                <div class="col-12">
                    <h6>Parent/Guardian Information</h6>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Parent/Guardian Name</label>
                    <input type="text" name="parent_guardian_name" class="form-control @error('parent_guardian_name') is-invalid @enderror" value="{{ old('parent_guardian_name', $student->parent_guardian_name) }}">
                    @error('parent_guardian_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Relationship</label>
                    <input type="text" name="parent_guardian_relationship" class="form-control @error('parent_guardian_relationship') is-invalid @enderror" value="{{ old('parent_guardian_relationship', $student->parent_guardian_relationship) }}" placeholder="e.g., Father, Mother, Guardian">
                    @error('parent_guardian_relationship')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Phone</label>
                    <input type="text" name="parent_guardian_phone" class="form-control @error('parent_guardian_phone') is-invalid @enderror" value="{{ old('parent_guardian_phone', $student->parent_guardian_phone) }}">
                    @error('parent_guardian_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="parent_guardian_email" class="form-control @error('parent_guardian_email') is-invalid @enderror" value="{{ old('parent_guardian_email', $student->parent_guardian_email) }}">
                    @error('parent_guardian_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-12"><hr></div>
                
                <div class="col-12">
                    <h6>Emergency Contact</h6>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Contact Name</label>
                    <input type="text" name="emergency_contact_name" class="form-control @error('emergency_contact_name') is-invalid @enderror" value="{{ old('emergency_contact_name', $student->emergency_contact_name) }}">
                    @error('emergency_contact_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="emergency_contact_phone" class="form-control @error('emergency_contact_phone') is-invalid @enderror" value="{{ old('emergency_contact_phone', $student->emergency_contact_phone) }}">
                    @error('emergency_contact_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Update Student
                </button>
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary ms-2">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
