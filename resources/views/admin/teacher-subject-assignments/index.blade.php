@extends('layouts.admin')

@section('title', 'Teacher Subject Assignments')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Teacher Subject Assignments</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Teacher Subject Assignments</h4>
            <div>
                <a href="{{ route('admin.teacher-subject-assignments.export') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
                <a href="{{ route('admin.teacher-subject-assignments.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Add Assignment
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.teacher-subject-assignments.index') }}" class="row g-3">
            <div class="col-md-2">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="teacher_id" class="form-select">
                    <option value="0">All Teachers</option>
                    @foreach($teachers as $id => $name)
                        <option value="{{ $id }}" {{ request('teacher_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="subject_id" class="form-select">
                    <option value="0">All Subjects</option>
                    @foreach($subjects as $id => $name)
                        <option value="{{ $id }}" {{ request('subject_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Search</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.teacher-subject-assignments.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i>Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Assignments Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Teacher</th>
                        <th>Subject</th>
                        <th>Class</th>
                        <th>Arm</th>
                        <th>Session</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->id }}</td>
                        <td>{{ $assignment->teacher->name ?? 'N/A' }}</td>
                        <td>{{ $assignment->subject->name ?? 'N/A' }}</td>
                        <td>{{ $assignment->classArm->class->name ?? 'N/A' }}</td>
                        <td>{{ $assignment->classArm->arm_name ?? 'N/A' }}</td>
                        <td>{{ $assignment->academicSession->name ?? 'N/A' }}</td>
                        <td>
                            @if($assignment->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.teacher-subject-assignments.edit', $assignment->id) }}" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.teacher-subject-assignments.destroy', $assignment->id) }}" method="POST" class="d-inline" onclick="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2 mb-0 text-muted">No teacher subject assignments found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $assignments->links() }}</div>
    </div>
</div>
@endsection
