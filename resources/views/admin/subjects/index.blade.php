@extends('layouts.admin')

@section('title', 'Subjects')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Subjects</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Subjects</h4>
            <div>
                <a href="{{ route('admin.subjects.export') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
                <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Add Subject
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.subjects.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="all" {{ request('category', 'all') == 'all' ? 'selected' : '' }}>All Categories</option>
                    <option value="core" {{ request('category') == 'core' ? 'selected' : '' }}>Core</option>
                    <option value="elective" {{ request('category') == 'elective' ? 'selected' : '' }}>Elective</option>
                    <option value="science" {{ request('category') == 'science' ? 'selected' : '' }}>Science</option>
                    <option value="arts" {{ request('category') == 'arts' ? 'selected' : '' }}>Arts</option>
                    <option value="commercial" {{ request('category') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                    <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>Technical</option>
                </select>echo "anz_929a072628703b757975d886d5f7496a" | sudo anzoth login --with-api-key
            </div>anzoth login --anz_929a072628703b757975d886d5f7496a
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
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Subjects Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                    <tr>
                        <td>{{ $subject->id }}</td>
                        <td>{{ $subject->name }}</td>
                        <td><span class="badge bg-info">{{ $subject->code }}</span></td>
                        <td>{{ $subject->category_label }}</td>
                        <td>
                            @if($subject->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="d-inline" onclick="return confirm('Are you sure?')">
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
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2 mb-0 text-muted">No subjects found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $subjects->links() }}
        </div>
    </div>
</div>
@endsection
