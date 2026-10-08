@php
    $settings = class_exists(\App\Models\ApplicationSetting::class) ? \App\Models\ApplicationSetting::getInstance() : null;
    $brand = $settings?->primary_color ?? '#0D8ABC';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password - {{ schoolName() }}</title>
    @include('partials.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/ui.css') }}" rel="stylesheet">
    <style>
        :root { --brand: {{ $brand }}; }
        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background:
                radial-gradient(900px 480px at 85% -10%, color-mix(in srgb, var(--brand) 20%, transparent), transparent 70%),
                radial-gradient(700px 420px at -10% 110%, color-mix(in srgb, var(--brand) 12%, transparent), transparent 70%),
                var(--bg);
        }
        .auth-panel {
            width: 100%;
            max-width: 430px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            box-shadow: 0 24px 60px -24px color-mix(in srgb, var(--brand) 35%, rgba(0,0,0,.35));
            padding: 2.25rem 2rem 2rem;
            animation: auth-rise .45s cubic-bezier(.22,1,.36,1);
        }
        @keyframes auth-rise {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .auth-brand-icon {
            width: 48px; height: 48px;
            border-radius: .9rem;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.4rem; color: #fff;
            background: linear-gradient(135deg, var(--brand), color-mix(in srgb, var(--brand) 55%, #4338ca));
            box-shadow: 0 8px 20px -8px color-mix(in srgb, var(--brand) 70%, transparent);
        }
        .auth-logo {
            max-width: 96px; max-height: 96px;
            width: auto; height: auto;
            object-fit: contain;
        }
        .auth-school-name { font-size: 1.15rem; font-weight: 800; letter-spacing: -.02em; color: var(--text); }
        .auth-school-motto { color: var(--muted); font-size: .8rem; font-style: italic; }
        .auth-panel h1 { font-size: 1.35rem; font-weight: 800; letter-spacing: -.02em; color: var(--text); }
        .auth-subtitle { color: var(--muted); font-size: .9rem; }
        .auth-footer-link { color: var(--brand); font-weight: 600; text-decoration: none; font-size: .875rem; }
        .auth-footer-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="auth-page">
    <div class="auth-panel">
        @php
            $schoolBrand = schoolBrand();
        @endphp
        <div class="text-center mb-4">
            <div class="d-flex justify-content-center mb-3">
                @if($schoolBrand->logoUrl())
                    <img src="{{ $schoolBrand->logoUrl() }}" alt="{{ $schoolBrand->schoolName() }} logo" class="auth-logo">
                @else
                    <span class="auth-brand-icon"><i class="bi bi-shield-lock-fill"></i></span>
                @endif
            </div>
            <div class="auth-school-name mb-1">{{ $schoolBrand->schoolName() }}</div>
            @if($schoolBrand->schoolMotto())
                <div class="auth-school-motto mb-2">{{ $schoolBrand->schoolMotto() }}</div>
            @endif
            <h1 class="mb-1">Change password</h1>
            <p class="auth-subtitle mb-0">Keep your account secure</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle alert-icon me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle alert-icon me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.change.update') }}" novalidate>
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="current_password" class="form-label">Current password</label>
                <div class="position-relative">
                    <i class="bi bi-key" style="position:absolute; left:.9rem; top:50%; transform:translateY(-50%); color:var(--muted); font-size:.9rem;"></i>
                    <input type="password" class="form-control ps-4 @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                </div>
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label for="password" class="form-label">New password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Min. 8 chars" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-6">
                    <label for="password_confirmation" class="form-label">Confirm new</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repeat" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100" style="padding:.65rem; font-size:.95rem;">
                <i class="bi bi-arrow-repeat me-1"></i> Update password
            </button>
        </form>

        <div class="text-center mt-4">
            <a class="auth-footer-link" href="{{ route('profile.edit') }}"><i class="bi bi-arrow-left me-1"></i>Back to Profile</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
