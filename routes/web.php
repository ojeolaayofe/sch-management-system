<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AcademicSessionController;
use App\Http\Controllers\Admin\AcademicTermController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ClassArmController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TeacherSubjectAssignmentController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\StudentResultController;
use App\Http\Controllers\Admin\ResultsController;

// Home Route
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (Laravel Breeze style)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post('login', function (\Illuminate\Http\Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        if (\Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }
            
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    });
    
});

// Logout Route
Route::post('logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Profile Routes
    Route::get('profile', function (\Illuminate\Http\Request $request) {
        return view('profile.edit', ['user' => $request->user()]);
    })->name('profile.edit');
    
    Route::put('profile', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);
        
        $request->user()->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);
        
        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    })->name('profile.update');
    
    Route::delete('profile', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);
        
        $user = $request->user();
        \Illuminate\Support\Facades\Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();  
        user_error;
        
        return redirect('/');
    })->name('profile.destroy');
    
    // Change Password
    Route::get('change-password', function () {
        return view('auth.change-password');
    })->name('password.change');
    
    Route::put('change-password', function (\Illuminate\Http\Request $request) {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        $request->user()->update([
            'password' => bcrypt($validated['password']),
        ]);
        
        return redirect()->route('profile.edit')->with('success', 'Password changed successfully.');
    })->name('password.change.update');
    
    // Teacher Routes
    Route::prefix('teacher')->name('teacher.')->middleware('role:teacher')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('home');
        Route::get('/classes', [\App\Http\Controllers\Teacher\ClassController::class, 'index'])->name('classes.index');
        Route::get('/subjects', [\App\Http\Controllers\Teacher\SubjectController::class, 'index'])->name('subjects.index');

        // CA Score Entry
        Route::get('/ca-scores', [\App\Http\Controllers\Teacher\CAScoreController::class, 'index'])->name('ca-scores.index');
        Route::get('/ca-scores/create', [\App\Http\Controllers\Teacher\CAScoreController::class, 'create'])->name('ca-scores.create');
        Route::post('/ca-scores', [\App\Http\Controllers\Teacher\CAScoreController::class, 'store'])->name('ca-scores.store');

        // Examination Score Entry
        Route::get('/exam-scores', [\App\Http\Controllers\Teacher\ExamScoreController::class, 'index'])->name('exam-scores.index');
        Route::get('/exam-scores/create', [\App\Http\Controllers\Teacher\ExamScoreController::class, 'create'])->name('exam-scores.create');
        Route::post('/exam-scores', [\App\Http\Controllers\Teacher\ExamScoreController::class, 'store'])->name('exam-scores.store');

        // Attendance
        Route::get('/attendance', [\App\Http\Controllers\Teacher\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [\App\Http\Controllers\Teacher\AttendanceController::class, 'store'])->name('attendance.store');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            // Only accounts with an admin role may view the admin dashboard.
            // Teachers (and any account without a role) are rejected by the
            // role middleware below.
            return view('admin.dashboard.index');
        })->name('dashboard')->middleware('role:super_admin|school_administrator');
        
        // Academic Structure Routes
        Route::middleware('role:super_admin|school_administrator')->group(function () {
            // Settings
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
            Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
            
            // Academic Sessions
            Route::get('/academic-sessions/export', [AcademicSessionController::class, 'export'])->name('academic-sessions.export');
            Route::post('/academic-sessions/{id}/set-current', [AcademicSessionController::class, 'setCurrent'])->name('academic-sessions.set-current');
            Route::resource('academic-sessions', AcademicSessionController::class);
            
            // Academic Terms
            Route::get('/academic-terms/export', [AcademicTermController::class, 'export'])->name('academic-terms.export');
            Route::post('/academic-terms/{id}/set-current', [AcademicTermController::class, 'setCurrent'])->name('academic-terms.set-current');
            Route::resource('academic-terms', AcademicTermController::class);
            
            // Classes
            Route::get('/classes/export', [ClassController::class, 'export'])->name('classes.export');
            Route::resource('classes', ClassController::class)->except(['show']);
            
            // Class Arms
            Route::get('/class-arms/export', [ClassArmController::class, 'export'])->name('class-arms.export');
            Route::resource('class-arms', ClassArmController::class)->except(['show']);
            
            // Subjects
            Route::get('/subjects/export', [SubjectController::class, 'export'])->name('subjects.export');
            Route::resource('subjects', SubjectController::class)->except(['show']);
            
            // Teacher Subject Assignments
            Route::get('/teacher-subject-assignments/export', [TeacherSubjectAssignmentController::class, 'export'])->name('teacher-subject-assignments.export');
            Route::resource('teacher-subject-assignments', TeacherSubjectAssignmentController::class)->except(['show']);
            
            // Students
            // Student Result PDF (must be registered before the resource wildcard)
            Route::get('/students/{student}/result/pdf', [StudentResultController::class, 'pdf'])->name('students.result.pdf');

            Route::resource('students', StudentController::class);
            
            // Results Management (central results for a session/term/class/arm)
            Route::get('/results/students/{student}/result/pdf', [ResultsController::class, 'pdf'])->name('results.students.pdf');
            Route::get('/results/students/{student}', [ResultsController::class, 'show'])->name('results.students.show');
            Route::get('/results', [ResultsController::class, 'index'])->name('results.index');

            // Teachers
            Route::resource('teachers', TeacherController::class);
        });

    });
});
