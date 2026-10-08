@extends('layouts.admin')

@php
    $totals = $result['totals'];
    $termLabel = $result['term'] ? $result['term']->label : 'All Terms';
    $sessionLabel = $result['session']->name;
    $pdfParams = http_build_query([
        'academic_session_id' => $academic_session_id,
        'academic_term_id' => $academic_term_id ?? '',
    ]);
@endphp

@section('title', 'Student Result')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.results.index') }}">Results</a></li>
<li class="breadcrumb-item active">{{ $student->student_id }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h4 class="mb-0">
                Student Result
                <span class="badge bg-info align-middle">{{ $student->student_id }}</span>
            </h4>
            <div>
                <a href="{{ route('admin.results.index') }}?{{ $pdfParams }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back to Results
                </a>
                <a href="{{ route('admin.results.students.pdf', $student->id) }}?{{ $pdfParams }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Download PDF
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Context bar -->
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="row g-2">
            <div class="col-md-3"><strong>Student:</strong> {{ $student->full_name }}</div>
            <div class="col-md-3"><strong>Session:</strong> {{ $sessionLabel }}</div>
            <div class="col-md-3"><strong>Term:</strong> {{ $termLabel }}</div>
            <div class="col-md-3">
                <strong>Class / Arm:</strong>
                {{ $result['class'] ? $result['class']->name : '—' }}
                {{ $result['classArm'] ? '(' . $result['classArm']->arm_name . ')' : '' }}
            </div>
        </div>
    </div>
</div>

<!-- Overall summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="fs-3 fw-bold">{{ $totals['score'] }}</div>
                <div class="text-muted small">Total Score (of {{ $totals['max'] }})</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="fs-3 fw-bold">{{ $totals['percentage'] !== null ? $totals['percentage'] . '%' : '—' }}</div>
                <div class="text-muted small">Overall Percentage</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="fs-3 fw-bold">{{ $totals['grade'] ?? '—' }}</div>
                <div class="text-muted small">Overall Grade</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="fs-4 fw-bold mt-1">{{ $totals['remark'] ?? '—' }}</div>
                <div class="text-muted small">Remark</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Subject results -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Subject Results</h5></div>
            <div class="card-body">
                @if($result['subjects']->isEmpty())
                    <p class="text-muted mb-0"><i class="bi bi-inbox me-1"></i>No scores recorded for this student in the selected session/term.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th class="text-center">CA</th>
                                    <th class="text-center">CA Test</th>
                                    <th class="text-center">Exam</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">%</th>
                                    <th class="text-center">Grade</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result['subjects'] as $row)
                                    <tr>
                                        <td class="fw-semibold">{{ $row['subject']->name }}</td>
                                        <td class="text-center">{{ $row['ca'] !== null ? $row['ca'] . ' / ' . $row['ca_max'] : '—' }}</td>
                                        <td class="text-center">{{ $row['ca_test'] !== null ? $row['ca_test'] . ' / ' . $row['ca_test_max'] : '—' }}</td>
                                        <td class="text-center">{{ $row['exam'] !== null ? $row['exam'] . ' / ' . $row['exam_max'] : '—' }}</td>
                                        <td class="text-center fw-semibold">{{ $row['total'] }} / {{ $row['total_max'] }}</td>
                                        <td class="text-center">{{ $row['percentage'] !== null ? $row['percentage'] . '%' : '—' }}</td>
                                        <td class="text-center">{{ $row['grade'] ? $row['grade']['grade'] : '—' }}</td>
                                        <td>{{ $row['grade'] ? $row['grade']['remark'] : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td>Overall</td>
                                    <td colspan="3"></td>
                                    <td class="text-center">{{ $totals['score'] }} / {{ $totals['max'] }}</td>
                                    <td class="text-center">{{ $totals['percentage'] !== null ? $totals['percentage'] . '%' : '—' }}</td>
                                    <td class="text-center">{{ $totals['grade'] ?? '—' }}</td>
                                    <td>{{ $totals['remark'] ?? '—' }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Attendance + remarks -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Attendance</h5></div>
            <div class="card-body">
                @if($result['attendance'])
                    @php $a = $result['attendance']; @endphp
                    <div class="row">
                        <div class="col-6 mb-2"><strong>Days recorded:</strong> {{ $a['days'] }}</div>
                        <div class="col-6 mb-2"><strong>Present:</strong> {{ $a['present'] }}</div>
                        <div class="col-6 mb-2"><strong>Absent:</strong> {{ $a['absent'] }}</div>
                        <div class="col-6 mb-2"><strong>Late:</strong> {{ $a['late'] }}</div>
                        <div class="col-6 mb-2"><strong>Excused:</strong> {{ $a['excused'] }}</div>
                    </div>
                @else
                    <p class="text-muted mb-0">No attendance data for the selected period.</p>
                @endif
            </div>
        </div>

        @if($result['remark'] && ($result['remark']->teacher_remark || $result['remark']->principal_remark))
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Remarks</h5></div>
                <div class="card-body">
                    <p class="mb-2"><strong>Teacher:</strong> {{ $result['remark']->teacher_remark ?: '—' }}</p>
                    <p class="mb-0"><strong>Principal:</strong> {{ $result['remark']->principal_remark ?: '—' }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
