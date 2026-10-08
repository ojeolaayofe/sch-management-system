@extends('layouts.admin')

@section('title', 'Settings')

@section('breadcrumbs')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
@php
    $s = $settings;
@endphp

<div class="page-header">
    <div>
        <h1 class="page-title">Settings</h1>
        <p class="page-subtitle">Configure school information and application preferences</p>
    </div>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header">
                    <div>
                        <h2 class="card-title"><i class="bi bi-building text-brand me-2"></i>School Information</h2>
                        <p class="card-subtitle">General details about your school</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="school_name" class="form-label">School Name</label>
                            <input type="text" class="form-control" id="school_name" name="school_name" value="{{ old('school_name', $s->school_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="school_motto" class="form-label">School Motto</label>
                            <input type="text" class="form-control" id="school_motto" name="school_motto" value="{{ old('school_motto', $s->school_motto) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="school_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="school_email" name="school_email" value="{{ old('school_email', $s->school_email) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $s->phone_number) }}">
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2">{{ old('address', $s->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <div>
                        <h2 class="card-title"><i class="bi bi-calendar-event text-brand me-2"></i>Academic Defaults</h2>
                        <p class="card-subtitle">Default academic context for the application</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="academic_session" class="form-label">Default Academic Session</label>
                            <input type="text" class="form-control" id="academic_session" name="academic_session" value="{{ old('academic_session', $s->academic_session) }}" placeholder="e.g. 2026/2027">
                        </div>
                        <div class="col-md-6">
                            <label for="current_term" class="form-label">Current Term</label>
                            <input type="text" class="form-control" id="current_term" name="current_term" value="{{ old('current_term', $s->current_term) }}" placeholder="e.g. First Term">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <div>
                        <h2 class="card-title"><i class="bi bi-palette text-brand me-2"></i>Branding</h2>
                        <p class="card-subtitle">Logo and theme colors</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="school_logo" class="form-label">School Logo</label>
                        @if($s->school_logo)
                            <div class="mb-2 d-flex align-items-center gap-2">
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($s->school_logo) }}" alt="Current logo"
                                     style="max-height: 72px; max-width: 72px; object-fit: contain; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px;">
                                <span class="text-muted-2 small">Current logo (upload a new one to replace)</span>
                            </div>
                        @endif
                        <input type="file" class="form-control" id="school_logo" name="school_logo" accept="image/*">
                        <div class="form-text">PNG or JPG, square images look best.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label for="primary_color" class="form-label">Primary Color</label>
                            <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="{{ old('primary_color', $s->primary_color ?? '#0D8ABC') }}">
                        </div>
                        <div class="col-6">
                            <label for="secondary_color" class="form-label">Secondary Color</label>
                            <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="{{ old('secondary_color', $s->secondary_color ?? '#10B981') }}">
                        </div>
                    </div>
                    <div class="form-text mt-2">The primary color is applied across the whole interface, including this admin panel.</div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <div>
                        <h2 class="card-title"><i class="bi bi-globe text-brand me-2"></i>Preferences</h2>
                        <p class="card-subtitle">Region and format settings</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="timezone" class="form-label">Timezone</label>
                            <input type="text" class="form-control" id="timezone" name="timezone" value="{{ old('timezone', $s->timezone) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="currency" class="form-label">Currency</label>
                            <input type="text" class="form-control" id="currency" name="currency" value="{{ old('currency', $s->currency) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Save Settings
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
