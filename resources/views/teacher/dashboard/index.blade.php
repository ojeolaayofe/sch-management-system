@extends('layouts.admin')

@section('title', 'Teacher Dashboard')

@section('breadcrumbs')
<li class="breadcrumb-item active">Teacher Dashboard</li>
@endsection

@section('content')
@php
    $currentSession = \App\Models\AcademicSession::getCurrent();
    $currentTerm = \App\Models\AcademicTerm::getCurrent();
@endphp

<div class="page-header">
    <div>
        <h1 class="page-title">Teacher Dashboard</h1>
        <p class="page-subtitle">Welcome, {{ $teacher->full_name }} ({{ $teacher->teacher_id }})</p>
    </div>
</div>

{{-- Current Session/Term --}}
@if($currentSession)
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="stat-icon brand"><i class="bi bi-calendar-event"></i></span>
                <div>
                    <div class="text-muted-2 small">Current Academic Session</div>
                    <div class="fw-bold">{{ $currentSession->name }}</div>
                    <div class="text-muted-2 small">{{ $currentSession->start_date->format('d M Y') }} — {{ $currentSession->end_date->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="stat-icon success"><i class="bi bi-calendar-week"></i></span>
                <div>
                    <div class="text-muted-2 small">Current Term</div>
                    <div class="fw-bold">{{ $currentTerm ? ($currentTerm->name_label ?? $currentTerm->name) : 'Not Set' }}</div>
                    @if($currentTerm)
                    <div class="text-muted-2 small">{{ $currentTerm->start_date->format('d M Y') }} — {{ $currentTerm->end_date->format('d M Y') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="stat-icon brand"><i class="bi bi-buildings"></i></span>
                <div>
                    <div class="stat-value">{{ $stats['assigned_classes_count'] }}</div>
                    <div class="stat-label">Assigned Classes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="stat-icon success"><i class="bi bi-journal-bookmark"></i></span>
                <div>
                    <div class="stat-value">{{ $stats['assigned_subjects_count'] }}</div>
                    <div class="stat-label">Assigned Subjects</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="stat-icon info"><i class="bi bi-people"></i></span>
                <div>
                    <div class="stat-value">{{ $stats['students_count'] }}</div>
                    <div class="stat-label">Students in My Classes</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick links --}}
<div class="row g-3">
    <div class="col-6 col-md-3">
        <a href="{{ route('teacher.classes.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body d-flex flex-column align-items-center text-center gap-2 py-4">
                    <span class="stat-icon brand"><i class="bi bi-buildings"></i></span>
                    <div class="fw-semibold">My Classes</div>
                    <div class="text-muted-2 small">View your assigned classes</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('teacher.subjects.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body d-flex flex-column align-items-center text-center gap-2 py-4">
                    <span class="stat-icon success"><i class="bi bi-journal-bookmark"></i></span>
                    <div class="fw-semibold">My Subjects</div>
                    <div class="text-muted-2 small">View your assigned subjects</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('teacher.ca-scores.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body d-flex flex-column align-items-center text-center gap-2 py-4">
                    <span class="stat-icon warning"><i class="bi bi-pencil-square"></i></span>
                    <div class="fw-semibold">CA Scores</div>
                    <div class="text-muted-2 small">Enter continuous assessment scores</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('teacher.exam-scores.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body d-flex flex-column align-items-center text-center gap-2 py-4">
                    <span class="stat-icon danger"><i class="bi bi-clipboard-data"></i></span>
                    <div class="fw-semibold">Exam Scores</div>
                    <div class="text-muted-2 small">Enter examination scores</div>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
