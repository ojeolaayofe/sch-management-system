@extends('layouts.admin')

@php
    $sessions = \App\Models\AcademicSession::where('status', 'active')->orderByDesc('start_date')->get()->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values();
    $termsForSession = \App\Models\AcademicTerm::where('status', 'active')->get()
        ->groupBy('academic_session_id')
        ->map(fn($g) => $g->sortBy('start_date')->map(fn($t) => ['id' => $t->id, 'label' => $t->label])->values())->all();
@endphp

@section('title', 'Student Profile')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.students.index') }}">Students</a></li>
<li class="breadcrumb-item active">{{ $student->student_id }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Student Profile</h4>
            <div>
                <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i>Edit
                </a>
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Student ID:</strong>
                        <p class="mb-0"><span class="badge bg-info">{{ $student->student_id }}</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Full Name:</strong>
                        <p class="mb-0">{{ $student->full_name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Gender:</strong>
                        <p class="mb-0">
                            @if($student->gender == 'male')
                                <span class="badge bg-primary">Male</span>
                            @else
                                <span class="badge bg-secondary">Female</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Date of Birth:</strong>
                        <p class="mb-0">{{ $student->date_of_birth ? $student->date_of_birth->format('d/m/Y') : '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Phone:</strong>
                        <p class="mb-0">{{ $student->phone ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Email:</strong>
                        <p class="mb-0">{{ $student->email ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Admission Date:</strong>
                        <p class="mb-0">{{ $student->admission_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Status:</strong>
                        <p class="mb-0">
                            @if($student->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($student->status == 'inactive')
                                <span class="badge bg-danger">Inactive</span>
                            @elseif($student->status == 'transferred')
                                <span class="badge bg-warning">Transferred</span>
                            @else
                                <span class="badge bg-info">Graduated</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <strong>Address:</strong>
                        <p class="mb-0">{{ $student->address ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-primary">
                        <i class="bi bi-mortarboard me-1"></i>Assign Class
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="bi bi-calendar-check me-1"></i>View Attendance
                    </a>
                </div>

                <hr>
                <h6 class="text-muted mb-2"><i class="bi bi-file-earmark-pdf me-1"></i>Download Result PDF</h6>
                <form method="GET" action="{{ route('admin.students.result.pdf', $student->id) }}" class="d-grid gap-2" id="result-pdf-form">
                    <select name="academic_session_id" class="form-select form-select-sm" id="result-session-select" required>
                        <option value="">Select session...</option>
                        @foreach($sessions as $s)
                            <option value="{{ $s['id'] }}" {{ old('academic_session_id') == $s['id'] ? 'selected' : '' }}>{{ $s['name'] }}</option>
                        @endforeach
                    </select>
                    <select name="academic_term_id" class="form-select form-select-sm" id="result-term-select">
                        <option value="">All terms</option>
                    </select>
                    <button type="submit" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-download me-1"></i>Generate PDF
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Parent/Guardian Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Name:</strong>
                        <p class="mb-0">{{ $student->parent_guardian_name ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Relationship:</strong>
                        <p class="mb-0">{{ $student->parent_guardian_relationship ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Phone:</strong>
                        <p class="mb-0">{{ $student->parent_guardian_phone ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Email:</strong>
                        <p class="mb-0">{{ $student->parent_guardian_email ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Emergency Contact</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Name:</strong>
                        <p class="mb-0">{{ $student->emergency_contact_name ?? '-' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>Phone:</strong>
                        <p class="mb-0">{{ $student->emergency_contact_phone ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const termsForSession = @json($termsForSession);
const sessionSelect = document.getElementById('result-session-select');
const termSelect = document.getElementById('result-term-select');

function populateTerms() {
    const sid = sessionSelect.value;
    termSelect.innerHTML = '<option value="">All terms</option>';
    if (sid && termsForSession[sid]) {
        termsForSession[sid].forEach(t => {
            termSelect.innerHTML += `<option value="${t.id}">${t.label}</option>`;
        });
    }
}
sessionSelect.addEventListener('change', populateTerms);
populateTerms();
</script>
@endpush
@endsection
