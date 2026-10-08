@extends('layouts.admin')

@section('title', 'Teachers')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Teachers</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Teachers</h4>
            <div>
                <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Add Teacher
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.teachers.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name, ID or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    <option value="resigned" {{ request('status') == 'resigned' ? 'selected' : '' }}>Resigned</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Search
                </button>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Teachers Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Teacher ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                    <tr>
                        <td>{{ $teacher->id }}</td>
                        <td><span class="badge bg-info">{{ $teacher->teacher_id }}</span></td>
                        <td>{{ $teacher->full_name }}</td>
                        <td>{{ $teacher->email ?? '-' }}</td>
                        <td>{{ $teacher->phone ?? '-' }}</td>
                        <td>
                            @if($teacher->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($teacher->status == 'inactive')
                                <span class="badge bg-danger">Inactive</span>
                            @elseif($teacher->status == 'on_leave')
                                <span class="badge bg-warning">On Leave</span>
                            @else
                                <span class="badge bg-secondary">Resigned</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.teachers.show', $teacher->id) }}" class="btn btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.teachers.destroy', $teacher->id) }}" method="POST" class="d-inline" onclick="return confirm('Are you sure you want to delete this teacher?')">
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
                            <p class="mt-2 mb-0 text-muted">No teachers found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $teachers->links() }}
        </div>
    </div>
</div>
@endsection
