@extends('layouts.admin')

@section('title', 'Teacher Profile')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.teachers.index') }}">Teachers</a></li>
<li class="breadcrumb-item active">{{ $teacher->teacher_id }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Teacher Profile</h4>
            <div>
                <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Teacher ID:</strong>
                        <p class="mb-0"><span class="badge bg-info">{{ $teacher->teacher_id }}</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Full Name:</strong>
                        <p class="mb-0">{{ $teacher->full_name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Email:</strong>
                        <p class="mb-0">{{ $teacher->email ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Phone:</strong>
                        <p class="mb-0">{{ $teacher->phone ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Qualification:</strong>
                        <p class="mb-0">{{ $teacher->qualification ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Employment Date:</strong>
                        <p class="mb-0">{{ $teacher->employment_date ? $teacher->employment_date->format('d/m/Y') : '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Status:</strong>
                        <p class="mb-0">
                            @if($teacher->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($teacher->status == 'inactive')
                                <span class="badge bg-danger">Inactive</span>
                            @elseif($teacher->status == 'on_leave')
                                <span class="badge bg-warning">On Leave</span>
                            @else
                                <span class="badge bg-secondary">Resigned</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <strong>Address:</strong>
                        <p class="mb-0">{{ $teacher->address ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary">
                        <i class="bi bi-book me-1"></i>Assign Subjects
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="bi bi-calendar-check me-1"></i>View Attendance
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-text me-1"></i>View Scores
                    </a>
                </div>
            </div>
        </div>
        
        @if($teacher->user)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Login Account</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Email:</strong> {{ $teacher->user->email }}</p>
                <p class="mb-0"><strong>Role:</strong> <span class="badge bg-primary">Teacher</span></p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
