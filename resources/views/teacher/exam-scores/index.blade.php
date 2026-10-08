@extends('layouts.admin')

@section('title', 'Examination Score Entry')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Exam Scores</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Examination Score Entry</h1>
        <p class="page-subtitle">Select a subject and class to enter term/examination scores</p>
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
                <p class="card-subtitle">Choose the subject, class, and session</p>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.exam-scores.create') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select" id="exam-subject-select" required>
                        <option value="">Select subject...</option>
                        @foreach($assignmentsBySubject as $subjectId => $group)
                            <option value="{{ $subjectId }}">{{ $group->first()->subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Class / Arm</label>
                    <select name="class_arm_id" class="form-select" id="exam-arm-select" required>
                        <option value="">Select class...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Academic Session</label>
                    <select name="academic_session_id" class="form-select" id="exam-session-select" required>
                        @foreach($sessions as $s)
                            <option value="{{ $s->id }}" {{ $currentSession && $currentSession->id === $s->id ? 'selected' : '' }}>{{ $s->name }}{{ $s->is_current ? ' (Current)' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Term</label>
                    <select name="academic_term_id" class="form-select" id="exam-term-select">
                        <option value="">Select term (optional)...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Max Score</label>
                    <input type="number" name="max_score" class="form-control" value="100" min="1" max="1000" step="1">
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary" id="exam-submit-btn" disabled>
                        <i class="bi bi-arrow-right me-1"></i> Enter Scores
                    </button>
                    <span class="text-muted-2 ms-2 small" id="exam-hint">Select subject and class to enable</span>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    const armData = @json($armData);
    const termsData = @json($termsData);

    const subjectSelect = document.getElementById('exam-subject-select');
    const armSelect = document.getElementById('exam-arm-select');
    const sessionSelect = document.getElementById('exam-session-select');
    const termSelect = document.getElementById('exam-term-select');
    const submitBtn = document.getElementById('exam-submit-btn');
    const hint = document.getElementById('exam-hint');

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

    function updateState() {
        const ready = subjectSelect.value && armSelect.value;
        submitBtn.disabled = !ready;
        hint.textContent = ready ? '' : 'Select subject and class to enable';
    }

    armSelect.addEventListener('change', updateState);
    </script>
    @endpush
@endif
@endsection
