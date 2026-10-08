@extends('layouts.admin')

@section('title', 'My Subjects')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">My Subjects</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">My Subjects</h1>
        <p class="page-subtitle">Subjects assigned to you</p>
    </div>
</div>

@if($subjects->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-journal-x display-4 text-muted-2"></i>
            <h3 class="mt-3 mb-1">No Subjects Assigned</h3>
            <p class="text-muted-2">You have not been assigned to any subjects yet.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Subject</th>
                            <th>Class</th>
                            <th>Class Arm</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $item)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="stat-icon brand" style="width:36px;height:36px;"><i class="bi bi-journal-bookmark"></i></span>
                                    <div>
                                        <div class="fw-semibold">{{ $item->subject->name }}</div>
                                        <div class="text-muted-2 small">{{ $item->subject->code }} · {{ $item->subject->category_label }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->class->name }}</td>
                            <td>{{ $item->class_arm->arm_name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
