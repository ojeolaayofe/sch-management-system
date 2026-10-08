@extends('layouts.admin')

@section('title', 'Attendance')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Attendance</li>
@endsection

@php
    $statusLabels = ['present' => 'Present', 'absent' => 'Absent', 'late' => 'Late', 'excused' => 'Excused'];
    $statusColors = ['present' => 'success', 'absent' => 'danger', 'late' => 'warning', 'excused' => 'info'];
@endphp

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Attendance</h1>
        <p class="page-subtitle">Mark attendance for your classes</p>
    </div>
    @if($assignedArms->isNotEmpty() && $classArm)
    <span class="badge bg-soft-brand">{{ $classArm->class->name }} — {{ $classArm->arm_name }}</span>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($assignedArms->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox display-4 text-muted-2"></i>
            <h3 class="mt-3 mb-1">No Classes Assigned</h3>
            <p class="text-muted-2">You have not been assigned to any classes yet. Ask your administrator to assign you to a class.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title"><i class="bi bi-sliders text-brand me-2"></i>Select Class &amp; Date</h2>
                <p class="card-subtitle">Choose the class and date to mark attendance</p>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.attendance.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Class / Arm</label>
                    <select name="class_arm_id" class="form-select" required>
                        @foreach($assignedArms as $arm)
                            <option value="{{ $arm->id }}" {{ $classArm && $classArm->id === $arm->id ? 'selected' : '' }}>
                                {{ $arm->class->name }} — {{ $arm->arm_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" max="{{ now()->toDateString() }}" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-right me-1"></i> Load
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($classArm && $students->isNotEmpty())
        <div class="card mt-3">
            <div class="card-header">
                <div>
                    <h2 class="card-title"><i class="bi bi-calendar-check text-brand me-2"></i>Mark Attendance</h2>
                    <p class="card-subtitle">
                        {{ $classArm->class->name }} — {{ $classArm->arm_name }} &middot;
                        {{ \Illuminate\Support\Carbon::parse($date)->format('d M Y') }}
                        @if($existing && $existing->isNotEmpty())
                            <span class="badge bg-soft-warning ms-1">Already marked — saving will update</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @foreach($statusLabels as $key => $label)
                        <span class="badge bg-{{ $statusColors[$key] }}">{{ $label }}</span>
                    @endforeach
                    <span class="ms-auto text-muted-2 small">
                        <span id="att-summary">0</span> / {{ $students->count() }} marked
                    </span>
                </div>

                <form method="POST" action="{{ route('teacher.attendance.store') }}">
                    @csrf
                    <input type="hidden" name="class_arm_id" value="{{ $classArm->id }}">
                    <input type="hidden" name="attendance_date" value="{{ $date }}">

                    <div class="table-responsive">
                        <table class="table align-middle" id="attendance-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px">#</th>
                                    <th>Student</th>
                                    <th>Student ID</th>
                                    <th style="min-width: 240px">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $i => $student)
                                <tr>
                                    <td class="text-muted-2">{{ $i + 1 }}</td>
                                    <td class="fw-semibold">{{ $student->full_name }}</td>
                                    <td class="text-muted-2 small">{{ $student->student_id }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm att-status-group" role="group">
                                            @foreach($statusLabels as $key => $label)
                                                <button type="button"
                                                        class="btn btn-{{ $statusColors[$key] }} att-btn"
                                                        data-status="{{ $key }}"
                                                        data-student-id="{{ $student->id }}">
                                                    {{ $label }}
                                                </button>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="statuses[]" class="att-input" value="" data-student-id="{{ $student->id }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex align-items-center gap-2 mt-3">
                        <button type="submit" class="btn btn-primary" id="att-save-btn" disabled>
                            <i class="bi bi-save me-1"></i> Save Attendance
                        </button>
                        <span class="text-muted-2 small" id="att-hint">Mark at least one student to enable saving</span>
                    </div>
                </form>
            </div>
        </div>
    @elseif($classArm)
        <div class="card mt-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-people display-4 text-muted-2"></i>
                <h3 class="mt-3 mb-1">No Students</h3>
                <p class="text-muted-2">There are no active students assigned to {{ $classArm->class->name }} — {{ $classArm->arm_name }}.</p>
            </div>
        </div>
    @endif
@endif

@push('scripts')
<script>
@if($classArm && $existing && $existing->isNotEmpty())
const existingAttendance = @json($existing);
@else
const existingAttendance = {};
@endif

document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('attendance-table');
    if (!table) return;

    const saveBtn = document.getElementById('att-save-btn');
    const hint = document.getElementById('att-hint');
    const summary = document.getElementById('att-summary');

    function updateState() {
        const marked = table.querySelectorAll('.att-input:not([value=""])').length;
        summary.textContent = marked;
        saveBtn.disabled = marked === 0;
        hint.textContent = marked === 0 ? 'Mark at least one student to enable saving' : '';
    }

    table.addEventListener('click', function (e) {
        const btn = e.target.closest('.att-btn');
        if (!btn) return;

        const group = btn.closest('.att-status-group');
        const status = btn.dataset.status;

        // Toggle off if the same status is clicked again.
        const input = group.parentElement.querySelector('.att-input');
        const isAlreadyActive = btn.classList.contains('active') && input.value === status;

        group.querySelectorAll('.att-btn').forEach(b => b.classList.remove('active'));

        if (!isAlreadyActive) {
            btn.classList.add('active');
            input.value = status;
        } else {
            input.value = '';
        }
        updateState();
    });

    // Pre-select statuses already saved for this class/date.
    for (const [studentId, status] of Object.entries(existingAttendance)) {
        const input = table.querySelector(`.att-input[data-student-id="${studentId}"]`);
        if (!input) continue;
        const btn = input.closest('td').querySelector(`.att-btn[data-status="${status}"]`);
        if (btn) {
            btn.click();
        }
    }

    updateState();
});
</script>
@endpush
@endsection
