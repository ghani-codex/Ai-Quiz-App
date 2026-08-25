<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\View\View;

class CourseBrowseController extends Controller
{
    public function index(): View
    {
        return view('public.courses.index', [
            'courses'    => Course::published()->with(['category', 'teacher'])->latest()->paginate(12),
            'categories' => Category::orderBy('sort_order')->get(),
        ]);
    }

    public function show(Course $course): View
    {
        abort_unless($course->is_published, 404);

        $course->load(['category', 'teacher', 'lectures.quizzes']);

        return view('public.courses.show', [
            'course'         => $course,
            'relatedCourses' => Course::published()
                ->where('category_id', $course->category_id)
                ->where('id', '!=', $course->id)
                ->take(3)
                ->get(),
        ]);
    }

    public function category(Category $category): View
    {
        return view('public.courses.category', [
            'category' => $category,
            'courses'  => Course::published()
                ->where('category_id', $category->id)
                ->with(['teacher'])
                ->paginate(12),
        ]);
    }
}
