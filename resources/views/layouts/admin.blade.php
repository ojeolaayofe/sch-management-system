<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ schoolName() }}</title>
    @include('partials.favicon')

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/ui.css') }}" rel="stylesheet">

    {{-- Brand color from settings (defaults to the design blue) --}}
    <style>
        @php
            $branding = app('school.branding');
            $appSettings = $branding->settings();
            $primaryColor = $appSettings?->primary_color ?: '#0D8ABC';
            $secondaryColor = $appSettings?->secondary_color ?: '#10B981';
        @endphp
        :root { --brand: {{ $primaryColor }}; --secondary: {{ $secondaryColor }}; }
    </style>

    {{-- Set theme before paint to avoid flash --}}
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('ui_theme');
                var dark = saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (dark) document.documentElement.classList.add('dark');
            } catch (e) {}
        })();
    </script>

    @stack('styles')
</head>
<body>
    <div class="app-wrapper d-flex">
        {{-- ============================================================
             Sidebar
             ============================================================ --}}
        <aside class="sidebar" id="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                @php
                    $branding = app('school.branding');
                    $schoolLogoUrl = $branding->logoUrl();
                @endphp
                @if($schoolLogoUrl)
                    <img src="{{ $schoolLogoUrl }}" alt="{{ $branding->schoolName() }} logo" class="sidebar-brand-logo">
                @else
                    <span class="sidebar-brand-icon"><i class="bi bi-mortarboard-fill"></i></span>
                @endif
                <span>
                    <span class="sidebar-brand-name d-block text-truncate">{{ $branding->schoolName() }}</span>
                    <span class="sidebar-brand-sub d-block">Admin Console</span>
                </span>
            </a>

            <nav class="flex-grow-1 overflow-auto" style="scrollbar-width: thin;">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('profile.*') || request()->routeIs('password.*') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    @hasrole('teacher')
                    <li class="nav-item"><div class="sidebar-section">My Workspace</div></li>
                    <li class="nav-item">
                        <a href="{{ route('teacher.classes.index') }}" class="nav-link {{ request()->routeIs('teacher.classes.*') ? 'active' : '' }}">
                            <i class="bi bi-buildings"></i> My Classes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('teacher.subjects.index') }}" class="nav-link {{ request()->routeIs('teacher.subjects.*') ? 'active' : '' }}">
                            <i class="bi bi-journal-bookmark"></i> My Subjects
                        </a>
                    </li>
                    <li class="nav-item"><div class="sidebar-section">Score Entry</div></li>
                    <li class="nav-item">
                        <a href="{{ route('teacher.ca-scores.index') }}" class="nav-link {{ request()->routeIs('teacher.ca-scores.*') ? 'active' : '' }}">
                            <i class="bi bi-pencil-square"></i> CA Scores
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('teacher.exam-scores.index') }}" class="nav-link {{ request()->routeIs('teacher.exam-scores.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-data"></i> Exam Scores
                        </a>
                    </li>
                    <li class="nav-item"><div class="sidebar-section">Attendance</div></li>
                    <li class="nav-item">
                        <a href="{{ route('teacher.attendance.index') }}" class="nav-link {{ request()->routeIs('teacher.attendance.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-check"></i> Mark Attendance
                        </a>
                    </li>
                    @endhasrole

                    @hasanyrole('super_admin|school_administrator')
                    <li class="nav-item"><div class="sidebar-section">Academic Structure</div></li>

                    <li class="nav-item">
                        <a href="{{ route('admin.academic-sessions.index') }}" class="nav-link {{ request()->routeIs('admin.academic-sessions.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-event"></i> Academic Sessions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.academic-terms.index') }}" class="nav-link {{ request()->routeIs('admin.academic-terms.*') ? 'active' : '' }}">
                            <i class="bi bi-calendar-week"></i> Academic Terms
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.classes.index') }}" class="nav-link {{ request()->routeIs('admin.classes.*') ? 'active' : '' }}">
                            <i class="bi bi-buildings"></i> Classes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.class-arms.index') }}" class="nav-link {{ request()->routeIs('admin.class-arms.*') ? 'active' : '' }}">
                            <i class="bi bi-diagram-3"></i> Class Arms
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.subjects.index') }}" class="nav-link {{ request()->routeIs('admin.subjects.*') ? 'active' : '' }}">
                            <i class="bi bi-journal-bookmark"></i> Subjects
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.teacher-subject-assignments.index') }}" class="nav-link {{ request()->routeIs('admin.teacher-subject-assignments.*') ? 'active' : '' }}">
                            <i class="bi bi-person-workspace"></i> Teacher Assignments
                        </a>
                    </li>

                    <li class="nav-item"><div class="sidebar-section">People</div></li>

                    <li class="nav-item">
                        <a href="{{ route('admin.results.index') }}" class="nav-link {{ request()->routeIs('admin.results.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard-data"></i> Results
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.teachers.index') }}" class="nav-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
                            <i class="bi bi-person-badge"></i> Teachers
                        </a>
                    </li>
                    @endhasanyrole

                    @hasanyrole('super_admin|school_administrator')
                        <li class="nav-item"><div class="sidebar-section">Administration</div></li>
                        <li class="nav-item">
                            <a href="{{ url('admin/settings') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>
                    @endhasanyrole
                </ul>
            </nav>

            {{-- User card --}}
            <div class="sidebar-user mt-3">
                <img src="{{ auth()->user()->getAvatarUrl(64) }}" alt="{{ auth()->user()->name }}" onerror="this.style.display='none'">
                <div class="flex-grow-1 overflow-hidden">
                    <div class="sidebar-user-name text-truncate">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role text-truncate">{{ auth()->user()->roles->first()->name ?? 'User' }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn p-0 border-0 bg-transparent text-white-50" title="Logout" style="color: rgba(255,255,255,.7);">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </aside>

        <div class="sidebar-backdrop"></div>

        {{-- ============================================================
             Main column
             ============================================================ --}}
        <div class="main-content flex-grow-1">
            {{-- Topbar --}}
            <header class="topbar">
                <button class="topbar-btn d-md-none" data-ui="sidebar-open" title="Open menu">
                    <i class="bi bi-list"></i>
                </button>

                <nav class="topbar-breadcrumb d-none d-sm-flex" aria-label="breadcrumb">
                    @yield('breadcrumbs')
                </nav>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <div class="topbar-search d-none d-lg-block">
                        <i class="bi bi-search"></i>
                        <input type="search" placeholder="Search…" aria-label="Search">
                    </div>

                    <button id="themeToggle" class="topbar-btn" title="Toggle theme">
                        <i class="bi bi-moon-stars"></i>
                    </button>

                    <div class="dropdown">
                        <button type="button" class="topbar-profile" data-bs-toggle="dropdown" title="Profile">
                            <img src="{{ auth()->user()->getAvatarUrl(64) }}" alt="Avatar" onerror="this.style.display='none'">
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                            <i class="bi bi-chevron-down d-none d-md-inline" style="font-size:.65rem; opacity:.6;"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="px-3 py-2 border-bottom" style="border-color: var(--border) !important;">
                                    <div class="fw-semibold" style="font-size:.875rem;">{{ auth()->user()->name }}</div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ auth()->user()->email }}</div>
                                </div>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('password.change') }}"><i class="bi bi-key me-2"></i>Change Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item danger w-100 text-start"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="page flex-grow-1">
                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-check-circle alert-icon me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle alert-icon me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-exclamation-circle alert-icon me-2"></i>{{ session('warning') }}
                        <button type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                        <i class="bi bi-info-circle alert-icon me-2"></i>{{ session('info') }}
                        <button type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="font-size:.8rem; color: var(--muted); border-top: 1px solid var(--border); background: var(--surface);">
                <span>&copy; {{ date('Y') }} {{ schoolName() }}. All rights reserved.</span>
                <span>SchoolHub v1.0</span>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/ui.js') }}"></script>

    @stack('scripts')
</body>
</html>
