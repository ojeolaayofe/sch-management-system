@extends('layouts.admin')

@section('title', 'Class Arms')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Class Arms</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Class Arms</h4>
            <div>
                <a href="{{ route('admin.class-arms.export') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
                <a href="{{ route('admin.class-arms.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Add Class Arm
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.class-arms.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="class_id" class="form-select">
                    <option value="0">All Classes</option>
                    @foreach($classes as $id => $name)
                        <option value="{{ $id }}" {{ request('class_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Search
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.class-arms.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Class Arms Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Class</th>
                        <th>Arm</th>
                        <th>Capacity</th>
                        <th>Class Teacher</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($arms as $arm)
                    <tr>
                        <td>{{ $arm->id }}</td>
                        <td>{{ $arm->class->name ?? 'N/A' }}</td>
                        <td><span class="badge bg-primary">{{ $arm->arm_name }}</span></td>
                        <td>{{ $arm->capacity }}</td>
                        <td>{{ $arm->classTeacher->name ?? '-' }}</td>
                        <td>
                            @if($arm->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.class-arms.edit', $arm->id) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.class-arms.destroy', $arm->id) }}" method="POST" class="d-inline" onclick="return confirm('Are you sure?')">
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
                            <p class="mt-2 mb-0 text-muted">No class arms found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $arms->links() }}
        </div>
    </div>
</div>
@endsection
