<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Plan;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('public.landing', [
            'categories'      => Category::orderBy('sort_order')->take(6)->get(),
            'featuredCourses' => Course::featured()->with(['category', 'teacher'])->take(6)->get(),
            'plans'           => Plan::active()->take(4)->get(),
            'stats'           => [
                'students' => \App\Models\User::where('role', 'student')->count(),
                'teachers' => \App\Models\User::where('role', 'teacher')->where('is_approved', true)->count(),
                'courses'  => Course::published()->count(),
                'quizzes'  => \App\Models\Quiz::published()->count(),
            ],
            'topInstructors'  => \App\Models\User::teachers()
                ->where('is_approved', true)
                ->withCount('courses')
                ->take(4)
                ->get(),
        ]);
    }

    public function pricing(): View
    {
        return view('public.pricing', [
            'plans' => Plan::active()->get(),
        ]);
    }

    public function faq(): View
    {
        return view('public.faq');
    }
}
