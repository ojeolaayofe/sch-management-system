<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Result Sheet - {{ $result['student']->student_id }} - {{ $schoolName }}</title>
    <style>
        @page { size: A4; margin: 14mm 12mm; }
        * { box-sizing: border-box; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #1a1a1a; margin: 0; }

        .header { display: flex; align-items: center; border-bottom: 3px double #1f2d54; padding-bottom: 8px; margin-bottom: 14px; }
        .logo { width: 60px; height: 60px; object-fit: contain; margin-right: 14px; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; }
        .logo-placeholder { width: 60px; height: 60px; border: 1px solid #cbd5e1; border-radius: 4px; margin-right: 14px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 9px; text-align: center; }
        .school-name { font-size: 20px; font-weight: 700; color: #1f2d54; line-height: 1.1; }
        .school-sub { font-size: 10px; color: #475569; margin-top: 2px; }
        .school-contact { font-size: 9px; color: #64748b; margin-top: 3px; line-height: 1.4; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #1f2d54; margin: 14px 0 4px; }
        .doc-context { text-align: center; font-size: 11px; color: #334155; margin-bottom: 12px; }

        .info-grid { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .info-grid td { border: 1px solid #cbd5e1; padding: 5px 8px; vertical-align: top; }
        .info-grid .label { background: #f1f5f9; font-weight: 700; width: 110px; color: #334155; font-size: 10px; }
        .info-grid .value { font-size: 11px; }

        table.result { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.result th, table.result td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: center; }
        table.result thead th { background: #1f2d54; color: #fff; font-size: 10px; text-transform: uppercase; letter-spacing: 0.4px; }
        table.result tbody tr:nth-child(even) { background: #f8fafc; }
        table.result td.subject { text-align: left; font-weight: 600; }
        table.result tfoot td { background: #eef2ff; font-weight: 700; }

        .section-title { font-size: 12px; font-weight: 700; color: #1f2d54; text-transform: uppercase; letter-spacing: 0.6px; margin: 14px 0 6px; border-bottom: 2px solid #1f2d54; padding-bottom: 3px; }

        .summary-box { display: flex; gap: 10px; margin-bottom: 12px; }
        .summary-card { flex: 1; border: 1px solid #cbd5e1; border-radius: 4px; padding: 8px; text-align: center; }
        .summary-card .big { font-size: 16px; font-weight: 700; color: #1f2d54; }
        .summary-card .small { font-size: 9px; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }

        .remark-box { border: 1px solid #cbd5e1; border-radius: 4px; padding: 8px 10px; margin-bottom: 14px; background: #f8fafc; }
        .remark-box .line { margin-bottom: 4px; }
        .remark-box .line strong { display: inline-block; width: 130px; color: #334155; }

        .signatures { display: flex; justify-content: space-between; margin-top: 40px; }
        .signature { text-align: center; width: 30%; }
        .signature .line { border-top: 1px solid #1a1a1a; padding-top: 4px; font-size: 10px; color: #334155; }

        .footer { margin-top: 24px; text-align: center; font-size: 9px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 6px; }
        .na { color: #94a3b8; font-style: italic; }
    </style>
</head>
<body>
    {{-- ============ HEADER ============ --}}
    <div class="header">
        @if(!empty($logoDataUri))
            <img class="logo" src="{{ $logoDataUri }}" alt="{{ $schoolName }} logo">
        @elseif(!empty($logoPath) && str_starts_with($logoPath, 'http'))
            <img class="logo" src="{{ $logoPath }}" alt="{{ $schoolName }} logo">
        @else
            <div class="logo-placeholder">NO<br>LOGO</div>
        @endif
        <div>
            <div class="school-name">{{ $schoolName }}</div>
            @if($schoolMotto)
                <div class="school-sub">{{ $schoolMotto }}</div>
            @endif
            @php
                $contactParts = array_filter([
                    $schoolAddress ?? null,
                    $schoolPhone ? 'Tel: ' . $schoolPhone : null,
                    $schoolEmail,
                ]);
            @endphp
            @if(count($contactParts) > 0)
                <div class="school-contact">{{ implode(' &middot; ', $contactParts) }}</div>
            @endif
        </div>
    </div>

    <div class="doc-title">Student Result Sheet</div>
    <div class="doc-context">
        {{ $result['session']->name }} &mdash;
        {{ $result['term'] ? $result['term']->label : 'All Terms' }}
    </div>

    {{-- ============ STUDENT INFO ============ --}}
    <div class="section-title">Student Information</div>
    <table class="info-grid">
        <tr>
            <td class="label">Name</td>
            <td class="value">{{ $result['student']->full_name }}</td>
            <td class="label">Student ID</td>
            <td class="value">{{ $result['student']->student_id }}</td>
        </tr>
        <tr>
            <td class="label">Class</td>
            <td class="value">{{ $result['class'] ? $result['class']->name : '—' }}</td>
            <td class="label">Class Arm</td>
            <td class="value">{{ $result['classArm'] ? $result['classArm']->arm_name : '—' }}</td>
        </tr>
        <tr>
            <td class="label">Session</td>
            <td class="value">{{ $result['session']->name }}</td>
            <td class="label">Term</td>
            <td class="value">{{ $result['term'] ? $result['term']->label : 'All Terms' }}</td>
        </tr>
        <tr>
            <td class="label">Gender</td>
            <td class="value">{{ ucfirst($result['student']->gender ?? '') ?: '—' }}</td>
            <td class="label">Date of Birth</td>
            <td class="value">{{ $result['student']->date_of_birth ? $result['student']->date_of_birth->format('d/m/Y') : '—' }}</td>
        </tr>
    </table>

    {{-- ============ SUBJECT RESULTS ============ --}}
    <div class="section-title">Subject Results</div>
    @if($result['subjects']->isEmpty())
        <div class="remark-box">
            <span class="na">No scores recorded for this student in the selected session/term.</span>
        </div>
    @else
        <table class="result">
            <thead>
                <tr>
                    <th style="text-align:left; width: 26%">Subject</th>
                    <th>CA</th>
                    <th>CA Test</th>
                    <th>Exam</th>
                    <th>Total</th>
                    <th>%</th>
                    <th>Grade</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result['subjects'] as $row)
                    <tr>
                        <td class="subject">{{ $row['subject']->name }}</td>
                        <td>{{ $row['ca'] !== null ? $row['ca'] . ' / ' . $row['ca_max'] : '<span class="na">—</span>' }}</td>
                        <td>{{ $row['ca_test'] !== null ? $row['ca_test'] . ' / ' . $row['ca_test_max'] : '<span class="na">—</span>' }}</td>
                        <td>{{ $row['exam'] !== null ? $row['exam'] . ' / ' . $row['exam_max'] : '<span class="na">—</span>' }}</td>
                        <td><strong>{{ $row['total'] }} / {{ $row['total_max'] }}</strong></td>
                        <td>{{ $row['percentage'] !== null ? $row['percentage'] . '%' : '—' }}</td>
                        <td>{{ $row['grade'] ? $row['grade']['grade'] : '—' }}</td>
                        <td>{{ $row['grade'] ? $row['grade']['remark'] : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="subject">Overall</td>
                    <td colspan="3"></td>
                    <td>{{ $result['totals']['score'] }} / {{ $result['totals']['max'] }}</td>
                    <td>{{ $result['totals']['percentage'] !== null ? $result['totals']['percentage'] . '%' : '—' }}</td>
                    <td>{{ $result['totals']['grade'] ?? '—' }}</td>
                    <td>{{ $result['totals']['remark'] ?? '—' }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- ============ OVERALL SUMMARY ============ --}}
    <div class="summary-box">
        <div class="summary-card">
            <div class="big">{{ $result['totals']['score'] }}</div>
            <div class="small">Total Score (of {{ $result['totals']['max'] }})</div>
        </div>
        <div class="summary-card">
            <div class="big">{{ $result['totals']['percentage'] !== null ? $result['totals']['percentage'] . '%' : '—' }}</div>
            <div class="small">Overall Percentage</div>
        </div>
        <div class="summary-card">
            <div class="big">{{ $result['totals']['grade'] ?? '—' }}</div>
            <div class="small">Overall Grade</div>
        </div>
        <div class="summary-card">
            <div class="big">{{ $result['totals']['remark'] ?? '—' }}</div>
            <div class="small">Remark</div>
        </div>
    </div>

    {{-- ============ ATTENDANCE ============ --}}
    <div class="section-title">Attendance Summary</div>
    @if($result['attendance'])
        @php $a = $result['attendance']; @endphp
        <table class="result" style="width: 100%; margin-bottom: 8px;">
            <thead>
                <tr>
                    <th>Days Recorded</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Late</th>
                    <th>Excused</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ $a['days'] }}</strong></td>
                    <td>{{ $a['present'] }}</td>
                    <td>{{ $a['absent'] }}</td>
                    <td>{{ $a['late'] }}</td>
                    <td>{{ $a['excused'] }}</td>
                </tr>
            </tbody>
        </table>
        <div class="school-sub">Attendance reflects recorded attendance entries for the selected period.</div>
    @else
        <div class="remark-box"><span class="na">No attendance data available for the selected period.</span></div>
    @endif

    {{-- ============ REMARKS ============ --}}
    @if($result['remark'] && ($result['remark']->teacher_remark || $result['remark']->principal_remark))
        <div class="section-title">Remarks</div>
        <div class="remark-box">
            <div class="line"><strong>Teacher:</strong> {{ $result['remark']->teacher_remark ?: '—' }}</div>
            <div class="line"><strong>Principal:</strong> {{ $result['remark']->principal_remark ?: '—' }}</div>
        </div>
    @endif

    {{-- ============ SIGNATURES ============ --}}
    <div class="signatures">
        <div class="signature">
            <div class="line">Class Teacher</div>
        </div>
        <div class="signature">
            <div class="line">Principal</div>
        </div>
        <div class="signature">
            <div class="line">Parent / Guardian</div>
        </div>
    </div>

    {{-- ============ FOOTER ============ --}}
    <div class="footer">
        Generated on {{ $result['generated_at']->format('d M Y, h:i A') }} &middot; {{ $schoolName }}
    </div>
</body>
</html>
