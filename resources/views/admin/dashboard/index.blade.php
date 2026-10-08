@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumbs')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
@php
    $stats = [
        ['label' => 'Academic Sessions', 'value' => \App\Models\AcademicSession::count(), 'icon' => 'bi-calendar-event', 'tone' => 'brand',   'route' => 'admin.academic-sessions.index'],
        ['label' => 'Academic Terms',    'value' => \App\Models\AcademicTerm::count(),    'icon' => 'bi-calendar-week', 'tone' => 'success', 'route' => 'admin.academic-terms.index'],
        ['label' => 'Classes',           'value' => \App\Models\ClassModel::count(),      'icon' => 'bi-buildings',     'tone' => 'info',    'route' => 'admin.classes.index'],
        ['label' => 'Class Arms',        'value' => \App\Models\ClassArm::count(),        'icon' => 'bi-diagram-3',     'tone' => 'warning', 'route' => 'admin.class-arms.index'],
        ['label' => 'Subjects',          'value' => \App\Models\Subject::count(),         'icon' => 'bi-journal-bookmark', 'tone' => 'brand',  'route' => 'admin.subjects.index'],
        ['label' => 'Teachers',          'value' => \App\Models\Teacher::count(),         'icon' => 'bi-person-badge',  'tone' => 'success', 'route' => 'admin.teachers.index'],
        ['label' => 'Students',          'value' => \App\Models\Student::count(),         'icon' => 'bi-people',        'tone' => 'info',    'route' => 'admin.students.index'],
        ['label' => 'Assignments',       'value' => \App\Models\TeacherSubjectAssignment::count(), 'icon' => 'bi-person-workspace', 'tone' => 'danger', 'route' => 'admin.teacher-subject-assignments.index'],
    ];

    $currentSession = \App\Models\AcademicSession::getCurrent();
    $currentTerm    = \App\Models\AcademicTerm::getCurrent();
    $today = now()->format('D, d M Y');
@endphp

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Good to see you, {{ auth()->user()->name }} · {{ $today }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.students.create') }}" class="btn btn-outline-primary btn-sm d-none d-sm-inline-flex">
            <i class="bi bi-people"></i> Students
        </a>
        <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus"></i> Add Teacher
        </a>
    </div>
</div>

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    @foreach($stats as $stat)
    <div class="col-6 col-md-4 col-xl-3">
        <a href="{{ route($stat['route']) }}" class="text-decoration-none">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon {{ $stat['tone'] }}"><i class="bi {{ $stat['icon'] }}"></i></span>
                    <div>
                        <div class="stat-value">{{ number_format($stat['value']) }}</div>
                        <div class="stat-label">{{ $stat['label'] }}</div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

<div class="row g-3">
    {{-- Current session --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <div>
                    <h2 class="card-title"><i class="bi bi-calendar-event text-brand me-2"></i>Current Session</h2>
                    <p class="card-subtitle">The academic year the school is running</p>
                </div>
                <a href="{{ route('admin.academic-sessions.index') }}" class="btn btn-outline-primary btn-sm">Manage</a>
            </div>
            <div class="card-body">
                @if($currentSession)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="stat-icon brand"><i class="bi bi-calendar-check"></i></span>
                        <div>
                            <div class="fw-bold" style="font-size:1.05rem;">{{ $currentSession->name }}</div>
                            <div class="text-muted-2" style="font-size:.82rem;">{{ $currentSession->start_date->format('d M Y') }} — {{ $currentSession->end_date->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 99px; background: var(--surface-2);">
                        @php
                            $start = $currentSession->start_date->startOfDay();
                            $end   = $currentSession->end_date->endOfDay();
                            $nowTs = now()->timestamp;
                            $pct = $end->timestamp === $start->timestamp
                                ? 100
                                : max(0, min(100, round((($nowTs - $start->timestamp) / ($end->timestamp - $start->timestamp)) * 100)));
                        @endphp
                        <div class="progress-bar" style="width: {{ $pct }}%; background: linear-gradient(90deg, var(--brand), color-mix(in srgb, var(--brand) 60%, #4338ca)); border-radius: 99px;"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2" style="font-size:.75rem; color: var(--muted);">
                        <span>Progress</span><span class="fw-semibold text-brand">{{ $pct }}%</span>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="empty-icon mx-auto mb-3" style="width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;background:var(--brand-soft);color:var(--brand);"><i class="bi bi-calendar-x"></i></div>
                        <p class="text-muted-2 mb-2">No current session set</p>
                        <a href="{{ route('admin.academic-sessions.create') }}" class="btn btn-outline-primary btn-sm">Set a session</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Current term --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <div>
                    <h2 class="card-title"><i class="bi bi-calendar-week text-brand me-2"></i>Current Term</h2>
                    <p class="card-subtitle">The term in progress for the current session</p>
                </div>
                <a href="{{ route('admin.academic-terms.index') }}" class="btn btn-outline-primary btn-sm">Manage</a>
            </div>
            <div class="card-body">
                @if($currentTerm)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="stat-icon success"><i class="bi bi-calendar-check"></i></span>
                        <div>
                            <div class="fw-bold" style="font-size:1.05rem;">{{ $currentTerm->label }}</div>
                            <div class="text-muted-2" style="font-size:.82rem;">{{ $currentTerm->academicSession->name ?? 'N/A' }} · {{ $currentTerm->start_date->format('d M Y') }} — {{ $currentTerm->end_date->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 99px; background: var(--surface-2);">
                        @php
                            $tStart = $currentTerm->start_date->startOfDay();
                            $tEnd   = $currentTerm->end_date->endOfDay();
                            $tNow   = now()->timestamp;
                            $tPct = $tEnd->timestamp === $tStart->timestamp
                                ? 100
                                : max(0, min(100, round((($tNow - $tStart->timestamp) / ($tEnd->timestamp - $tStart->timestamp)) * 100)));
                        @endphp
                        <div class="progress-bar" style="width: {{ $tPct }}%; background: linear-gradient(90deg, var(--success), color-mix(in srgb, var(--success) 50%, #059669)); border-radius: 99px;"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2" style="font-size:.75rem; color: var(--muted);">
                        <span>Progress</span><span class="fw-semibold" style="color:var(--success);">{{ $tPct }}%</span>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="empty-icon mx-auto mb-3" style="width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;background:color-mix(in srgb, var(--success) 14%, transparent);color:var(--success);"><i class="bi bi-calendar-x"></i></div>
                        <p class="text-muted-2 mb-2">No current term set</p>
                        <a href="{{ route('admin.academic-terms.create') }}" class="btn btn-outline-primary btn-sm">Set a term</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title"><i class="bi bi-lightning-charge-fill text-brand me-2"></i>Quick Actions</h2>
                    <p class="card-subtitle">Jump straight to the most common tasks</p>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach([
                        ['icon' => 'bi-plus-circle',      'label' => 'Add Student',   'route' => 'admin.students.create', 'tone' => 'brand'],
                        ['icon' => 'bi-person-plus',      'label' => 'Add Teacher',   'route' => 'admin.teachers.create', 'tone' => 'success'],
                        ['icon' => 'bi-building-add',     'label' => 'Add Class',     'route' => 'admin.classes.create',  'tone' => 'info'],
                        ['icon' => 'bi-journal-plus',     'label' => 'Add Subject',   'route' => 'admin.subjects.create', 'tone' => 'warning'],
                        ['icon' => 'bi-diagram-3',        'label' => 'Add Class Arm', 'route' => 'admin.class-arms.create', 'tone' => 'brand'],
                        ['icon' => 'bi-person-workspace', 'label' => 'New Assignment','route' => 'admin.teacher-subject-assignments.create', 'tone' => 'danger'],
                    ] as $action)
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route($action['route']) }}" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-none" style="box-shadow: var(--shadow-sm); border: 1px solid var(--border); border-radius: var(--radius-sm);">
                                <div class="card-body text-center py-3">
                                    <span class="stat-icon {{ $action['tone'] }} mb-2" style="width:44px;height:44px;font-size:1.2rem;"><i class="bi {{ $action['icon'] }}"></i></span>
                                    <div class="fw-semibold" style="font-size:.83rem; color: var(--text);">{{ $action['label'] }}</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
