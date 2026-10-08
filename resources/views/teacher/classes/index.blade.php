@extends('layouts.admin')

@section('title', 'My Classes')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">My Classes</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">My Classes</h1>
        <p class="page-subtitle">Classes assigned to you</p>
    </div>
</div>

@if($classes->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox display-4 text-muted-2"></i>
            <h3 class="mt-3 mb-1">No Classes Assigned</h3>
            <p class="text-muted-2">You have not been assigned to any classes yet.</p>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach($classes as $item)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <div>
                        <h2 class="card-title"><i class="bi bi-buildings text-brand me-2"></i>{{ $item->class->name }}</h2>
                        <p class="card-subtitle">{{ $item->class_arm->arm_name }}</p>
                    </div>
                    <span class="badge bg-{{ $item->class->section === 'primary' ? 'info' : ($item->class->section === 'junior_secondary' ? 'warning' : 'success') }}">
                        {{ $item->class->section_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted-2 small mb-1">Subjects Taught</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($item->subjects as $subject)
                            <span class="badge bg-soft-brand">{{ $subject }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted-2 small">Students</span>
                            <div class="fw-bold">{{ $item->student_count }}</div>
                        </div>
                        <span class="stat-icon info"><i class="bi bi-people"></i></span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
