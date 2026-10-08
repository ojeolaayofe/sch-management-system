@extends('layouts.admin')

@section('title', 'CA Score Entry')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">CA Scores</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">CA Score Entry</h1>
        <p class="page-subtitle">Select a subject, class, and assessment type to enter continuous assessment scores</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($assignments->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox display-4 text-muted-2"></i>
            <h3 class="mt-3 mb-1">No Assignments</h3>
            <p class="text-muted-2">You have no subject assignments yet. Ask your administrator to assign you to subjects.</p>
        </div>
    </div>
@else
    {{-- Prepare data for JS --}}
    @php
        $armData = $assignments->groupBy('subject_id')->map(function ($group) {
            return $group->map(fn($a) => [
                'id' => $a->class_arm_id,
                'label' => $a->classArm->class->name . ' — ' . $a->classArm->arm_name,
            ])->values();
        });

        $termsData = \App\Models\AcademicTerm::where('status', 'active')
            ->with('academicSession')
            ->get()
            ->groupBy('academic_session_id')
            ->map(function ($group) {
                return $group->map(fn($t) => ['id' => $t->id, 'label' => $t->name_label ?? $t->name])->values();
            });
    @endphp

    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title"><i class="bi bi-sliders text-brand me-2"></i>Select Context</h2>
                <p class="card-subtitle">Choose the subject, class, session, and assessment type</p>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.ca-scores.create') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select" id="ca-subject-select" required>
                        <option value="">Select subject...</option>
                        @foreach($assignmentsBySubject as $subjectId => $group)
                            <option value="{{ $subjectId }}">{{ $group->first()->subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Class / Arm</label>
                    <select name="class_arm_id" class="form-select" id="ca-arm-select" required>
                        <option value="">Select class...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Assessment Type</label>
                    <select name="assessment_type_id" class="form-select" required>
                        <option value="">Select type...</option>
                        @foreach($assessmentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }} (Max: {{ $type->max_score }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Academic Session</label>
                    <select name="academic_session_id" class="form-select" id="ca-session-select" required>
                        @foreach($sessions as $s)
                            <option value="{{ $s->id }}" {{ $currentSession && $currentSession->id === $s->id ? 'selected' : '' }}>{{ $s->name }}{{ $s->is_current ? ' (Current)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Term</label>
                    <select name="academic_term_id" class="form-select" id="ca-term-select">
                        <option value="">Select term (optional)...</option>
                    </select>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary" id="ca-submit-btn" disabled>
                        <i class="bi bi-arrow-right me-1"></i> Enter Scores
                    </button>
                    <span class="text-muted-2 ms-2 small" id="ca-hint">Select subject and class to enable</span>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    const armData = @json($armData);
    const termsData = @json($termsData);

    const subjectSelect = document.getElementById('ca-subject-select');
    const armSelect = document.getElementById('ca-arm-select');
    const sessionSelect = document.getElementById('ca-session-select');
    const termSelect = document.getElementById('ca-term-select');
    const submitBtn = document.getElementById('ca-submit-btn');
    const hint = document.getElementById('ca-hint');

    subjectSelect.addEventListener('change', function() {
        const sid = this.value;
        armSelect.innerHTML = '<option value="">Select class...</option>';
        if (armData[sid]) {
            armData[sid].forEach(arm => {
                armSelect.innerHTML += `<option value="${arm.id}">${arm.label}</option>`;
            });
        }
        updateState();
    });

    sessionSelect.addEventListener('change', function() {
        const sessionId = this.value;
        termSelect.innerHTML = '<option value="">Select term (optional)...</option>';
        if (termsData[sessionId]) {
            termsData[sessionId].forEach(term => {
                termSelect.innerHTML += `<option value="${term.id}">${term.label}</option>`;
            });
        }
    });

    armSelect.addEventListener('change', updateState);
    subjectSelect.addEventListener('change', updateState);

    function updateState() {
        const ready = subjectSelect.value && armSelect.value;
        submitBtn.disabled = !ready;
        hint.textContent = ready ? '' : 'Select subject and class to enable';
    }
    </script>
    @endpush
@endif
@endsection
