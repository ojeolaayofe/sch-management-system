@extends('layouts.admin')

@section('title', 'Academic Terms')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Academic Terms</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Academic Terms</h4>
            <div>
                <a href="{{ route('admin.academic-terms.export') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
                <a href="{{ route('admin.academic-terms.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Add Term
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.academic-terms.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="session_id" class="form-select">
                    <option value="0">All Sessions</option>
                    @foreach($sessions as $id => $name)
                        <option value="{{ $id }}" {{ request('session_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
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
                <a href="{{ route('admin.academic-terms.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Terms Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Session</th>
                        <th>Term Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Current</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terms as $term)
                    <tr>
                        <td>{{ $term->id }}</td>
                        <td>{{ $term->academicSession->name ?? 'N/A' }}</td>
                        <td>{{ $term->label }}</td>
                        <td>{{ $term->start_date->format('d M Y') }}</td>
                        <td>{{ $term->end_date->format('d M Y') }}</td>
                        <td>
                            @if($term->is_current)
                                <span class="badge bg-success">Current</span>
                            @else
                                <span class="badge bg-secondary">Not Current</span>
                            @endif
                        </td>
                        <td>
                            @if($term->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.academic-terms.edit', $term->id) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(!$term->is_current)
                                <form action="{{ route('admin.academic-terms.set-current', $term->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Set as Current" onclick="return confirm('Set this term as current?')">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('admin.academic-terms.destroy', $term->id) }}" method="POST" class="d-inline" onclick="return confirm('Are you sure?')">
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
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2 mb-0 text-muted">No academic terms found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $terms->links() }}
        </div>
    </div>
</div>
@endsection
