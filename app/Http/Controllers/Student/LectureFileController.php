<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LectureFileController extends Controller
{
    /**
     * Force a download of the original lecture file.
     * The teacher uploaded it; the student gets a clean filename.
     */
    public function download(Lecture $lecture)
    {
        $this->guardEnrolled($lecture);

        abort_unless($lecture->file_path, 404, 'This lecture has no downloadable file.');
        abort_unless(Storage::disk('local')->exists($lecture->file_path), 404, 'File missing.');

        return Storage::disk('local')->download(
            $lecture->file_path,
            $this->prettyFilename($lecture),
        );
    }

    /**
     * Inline stream so the browser renders the file (PDFs in particular).
     * No temp public URL is created; the route itself is the auth boundary.
     */
    public function view(Lecture $lecture)
    {
        $this->guardEnrolled($lecture);

        abort_unless($lecture->file_path, 404, 'This lecture has no file.');
        abort_unless(Storage::disk('local')->exists($lecture->file_path), 404, 'File missing.');

        $mime = Storage::disk('local')->mimeType($lecture->file_path) ?: 'application/octet-stream';

        return Storage::disk('local')->response(
            $lecture->file_path,
            $this->prettyFilename($lecture),
            [
                'Content-Type'           => $mime,
                'Content-Disposition'    => 'inline; filename="' . $this->prettyFilename($lecture) . '"',
                'X-Content-Type-Options' => 'nosniff',
            ],
        );
    }

    /**
     * Returns 403 if user is not enrolled in the lecture's course or is not a student.
     */
    private function guardEnrolled(Lecture $lecture): void
    {
        $user = auth()->user();
        abort_unless($user, 401);

        // Admins can always view (helpful for testing); teachers can view their own.
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isTeacher() && $lecture->course?->teacher_id === $user->id) {
            return;
        }

        $enrolled = $lecture->course?->enrollments()
            ->where('user_id', $user->id)
            ->exists() ?? false;

        abort_unless($enrolled, 403, 'You must enrol in this course to access its files.');
    }

    private function prettyFilename(Lecture $lecture): string
    {
        $ext  = pathinfo($lecture->file_path, PATHINFO_EXTENSION) ?: 'bin';
        $slug = Str::slug($lecture->title ?: 'lecture') ?: 'lecture';
        return "{$slug}.{$ext}";
    }
}
