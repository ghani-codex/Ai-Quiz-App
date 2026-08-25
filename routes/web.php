<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CourseBrowseController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\NewsletterController;
use App\Http\Controllers\Student\LectureFileController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public marketing site
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseBrowseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseBrowseController::class, 'show'])->name('courses.show');
Route::get('/categories/{category:slug}', [CourseBrowseController::class, 'category'])->name('categories.show');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

// Authenticated role-aware redirect
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return match ($user->role) {
            User::ROLE_ADMIN   => redirect('/admin'),
            User::ROLE_TEACHER => $user->is_approved ? redirect('/teacher') : redirect('/pending-approval'),
            User::ROLE_STUDENT => redirect('/student'),
            default            => redirect('/'),
        };
    })->name('dashboard');

    Route::view('/pending-approval', 'pending-approval')->name('pending-approval');

    // Secure lecture file access — enrollment is verified inside the controller
    Route::get('/student/lectures/{lecture}/download', [LectureFileController::class, 'download'])
        ->name('student.lectures.download');
    Route::get('/student/lectures/{lecture}/view', [LectureFileController::class, 'view'])
        ->name('student.lectures.view');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
