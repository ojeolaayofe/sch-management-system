@extends('layouts.admin')

@section('title', 'Enter CA Scores')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('teacher.ca-scores.index') }}">CA Scores</a></li>
<li class="breadcrumb-item active">Enter Scores</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Enter CA Scores</h1>
        <p class="page-subtitle">
            {{ $subject->name }} · {{ $classArm->class->name }} ({{ $classArm->arm_name }}) ·
            {{ $assessmentType->name }} (Max: {{ $assessmentType->max_score }}) ·
            {{ $session->name }}
        </p>
    </div>
    <a href="{{ route('teacher.ca-scores.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Change Selection
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong><i class="bi bi-exclamation-triangle me-2"></i>Validation Errors:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($students->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-people display-4 text-muted-2"></i>
            <h3 class="mt-3 mb-1">No Students</h3>
            <p class="text-muted-2">There are no students assigned to this class yet.</p>
        </div>
    </div>
@else
    <form method="POST" action="{{ route('teacher.ca-scores.store') }}">
        @csrf
        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
        <input type="hidden" name="class_arm_id" value="{{ $classArm->id }}">
        <input type="hidden" name="assessment_type_id" value="{{ $assessmentType->id }}">
        <input type="hidden" name="academic_session_id" value="{{ $session->id }}">
        <input type="hidden" name="academic_term_id" value="{{ old('academic_term_id', request('academic_term_id')) }}">

        <div class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title"><i class="bi bi-pencil-square text-brand me-2"></i>Student Scores</h2>
                    <p class="card-subtitle">Maximum score: {{ $assessmentType->max_score }} · Enter scores below (leave blank to skip)</p>
                </div>
                @if($terms->isNotEmpty())
                <select name="academic_term_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                    <option value="">No Term</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ old('academic_term_id', request('academic_term_id')) == $term->id ? 'selected' : '' }}>{{ $term->name_label ?? $term->name }}</option>
                    @endforeach
                </select>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width:120px">Student ID</th>
                                <th>Student Name</th>
                                <th style="width:160px">Score (out of {{ $assessmentType->max_score }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td class="ps-3 text-muted-2">{{ $student->student_id }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $student->last_name }}</span> {{ $student->first_name }} {{ $student->middle_name }}
                                </td>
                                <td>
                                    <input type="number"
                                        class="form-control form-control-sm"
                                        name="scores[{{ $student->id }}]"
                                        min="0"
                                        max="{{ $assessmentType->max_score }}"
                                        step="0.01"
                                        placeholder="0.00"
                                        value="{{ $existingScores[$student->id] ?? old("scores.$student.id") }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted-2 small">{{ $students->count() }} student(s)</span>
                    <div>
                        <a href="{{ route('teacher.ca-scores.index') }}" class="btn btn-outline-secondary btn-sm me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-check-lg me-1"></i> Save Scores
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endif
@endsection
