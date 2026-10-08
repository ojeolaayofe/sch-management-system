@extends('layouts.admin')

@section('title', 'Students')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Students</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Students</h4>
            <div>
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Add Student
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.students.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name or ID..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                    <option value="graduated" {{ request('status') == 'graduated' ? 'selected' : '' }}>Graduated</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Search
                </button>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Students Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Full Name</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>{{ $student->id }}</td>
                        <td><span class="badge bg-info">{{ $student->student_id }}</span></td>
                        <td>{{ $student->full_name }}</td>
                        <td>
                            @if($student->gender == 'male')
                                <span class="badge bg-primary">Male</span>
                            @else
                                <span class="badge bg-secondary">Female</span>
                            @endif
                        </td>
                        <td>{{ $student->phone ?? '-' }}</td>
                        <td>
                            @if($student->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($student->status == 'inactive')
                                <span class="badge bg-danger">Inactive</span>
                            @elseif($student->status == 'transferred')
                                <span class="badge bg-warning">Transferred</span>
                            @else
                                <span class="badge bg-info">Graduated</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="d-inline" onclick="return confirm('Are you sure you want to delete this student?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2 mb-0 text-muted">No students found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
