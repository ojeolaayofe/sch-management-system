@extends('layouts.admin')

@php
    $termsBySession = \App\Models\AcademicTerm::where('status', 'active')
        ->orderBy('start_date')
        ->get()
        ->groupBy('academic_session_id')
        ->map(fn ($g) => $g->map(fn ($t) => ['id' => $t->id, 'label' => $t->label])->values()->all())
        ->all();
    $armsByClass = \App\Models\ClassArm::with('class:id,name')
        ->orderBy('arm_name')
        ->get()
        ->groupBy('class_id')
        ->map(fn ($g) => $g->map(fn ($a) => ['id' => $a->id, 'name' => $a->arm_name])->values()->all())
        ->all();
@endphp

@section('title', 'Results Management')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Results</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="bi bi-clipboard-data me-2"></i>Results Management</h4>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-funnel me-1"></i>Select Class Arm</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.results.index') }}" class="row g-3" id="results-filter-form">
            <div class="col-md-3">
                <label class="form-label mb-1">Academic Session</label>
                <select name="academic_session_id" id="filter-session" class="form-select" required>
                    <option value="">Select session...</option>
                    @foreach($sessions as $s)
                        <option value="{{ $s['id'] }}" {{ $selected['academic_session_id'] == $s['id'] ? 'selected' : '' }}>{{ $s['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Academic Term</label>
                <select name="academic_term_id" id="filter-term" class="form-select">
                    <option value="">All terms</option>
                    @foreach($terms as $t)
                        <option value="{{ $t['id'] }}" {{ $selected['academic_term_id'] == $t['id'] ? 'selected' : '' }}>{{ $t['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Class</label>
                <select name="class_id" id="filter-class" class="form-select" required>
                    <option value="">Select class...</option>
                    @foreach($classes as $id => $name)
                        <option value="{{ $id }}" {{ $selected['class_id'] == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1">Class Arm</label>
                <select name="class_arm_id" id="filter-arm" class="form-select" required>
                    <option value="">Select arm...</option>
                    @foreach($arms as $a)
                        <option value="{{ $a['id'] }}" {{ $selected['class_arm_id'] == $a['id'] ? 'selected' : '' }}>{{ $a['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>View Results
                </button>
                <a href="{{ route('admin.results.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
</div>

@if($selected['class_arm_id'])
<!-- Results Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            Students
            @if(!empty($rows))
                <span class="text-muted fw-normal">({{ count($rows) }})</span>
            @endif
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Admission No.</th>
                        <th class="text-center">Subjects</th>
                        <th class="text-center">Total Score</th>
                        <th class="text-center">Average / %</th>
                        <th>Result Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $s = $row['summary'];
                            $status = $row['status'];
                            $viewParams = http_build_query([
                                'academic_session_id' => $selected['academic_session_id'],
                                'academic_term_id' => $selected['academic_term_id'] ?? '',
                            ]);
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $row['student']->full_name }}</td>
                            <td><span class="badge bg-info">{{ $row['student']->student_id }}</span></td>
                            <td class="text-center">{{ $s['subject_count'] }}</td>
                            <td class="text-center">
                                @if($s['total_score'] !== null)
                                    {{ $s['total_score'] }} / {{ $s['total_max'] }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($s['percentage'] !== null)
                                    {{ number_format($s['percentage'], 1) }}%
                                    @if($s['grade'])
                                        <span class="badge bg-dark ms-1">{{ $s['grade'] }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $status['badge'] }}">{{ $status['label'] }}</span></td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.results.students.show', $row['student']->id) . '?' . $viewParams }}"
                                       class="btn btn-outline-info" title="View Result">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.results.students.pdf', $row['student']->id) . '?' . $viewParams }}"
                                       class="btn btn-outline-success" title="Download PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox me-1"></i>No students found for the selected class arm.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    const termsBySession = @json($termsBySession);
    const armsByClass = @json($armsByClass);

    const filterSession = document.getElementById('filter-session');
    const filterTerm = document.getElementById('filter-term');
    const filterClass = document.getElementById('filter-class');
    const filterArm = document.getElementById('filter-arm');

    function repopulate(el, options, placeholder, selectedId) {
        el.innerHTML = `<option value="">${placeholder}</option>`;
        options.forEach(o => {
            const opt = document.createElement('option');
            opt.value = o.id;
            opt.textContent = o.name || o.label;
            if (String(o.id) === String(selectedId)) opt.selected = true;
            el.appendChild(opt);
        });
    }

    filterSession?.addEventListener('change', () => {
        repopulate(filterTerm, termsBySession[filterSession.value] || [], 'All terms', filterTerm.value);
    });

    filterClass?.addEventListener('change', () => {
        repopulate(filterArm, armsByClass[filterClass.value] || [], 'Select arm...', filterArm.value);
    });
</script>
@endpush
