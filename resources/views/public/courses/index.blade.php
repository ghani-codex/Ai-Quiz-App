@extends('layouts.public')

@section('title', 'Browse Courses')

@section('body_class', 'courses-page')

@section('content')
    <section class="hero section">
        <div class="container" data-aos="fade-up">
            <h1>Browse Courses</h1>
            <p>{{ $courses->total() }} published {{ Str::plural('course', $courses->total()) }} across {{ $categories->count() }} {{ Str::plural('category', $categories->count()) }}.</p>
        </div>
    </section>

    <section class="services section">
        <div class="container">
            <div class="row gy-4 mb-5">
                @foreach($categories as $cat)
                    <div class="col-lg-2 col-md-4 col-6 text-center">
                        <a href="{{ route('categories.show', $cat) }}" class="text-decoration-none">
                            <i class="bi {{ $cat->icon ?? 'bi-book' }}" style="font-size: 2rem; color: var(--accent-color);"></i>
                            <div class="mt-2 small">{{ $cat->name }}</div>
                        </a>
                    </div>
                @endforeach
            </div>

            @if($courses->isEmpty())
                <div class="text-center py-5">
                    <h4>No courses published yet.</h4>
                    <p>Be the first to <a href="{{ route('register') }}">teach a course</a>.</p>
                </div>
            @else
                <div class="row gy-4">
                    @foreach($courses as $course)
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100">
                                <img src="{{ $course->thumbnail ? asset('storage/'.$course->thumbnail) : asset('flexstart/img/portfolio/app-'.(($loop->index % 3)+1).'.jpg') }}" class="card-img-top" alt="">
                                <div class="card-body">
                                    <span class="badge bg-light text-dark mb-2">{{ $course->category->name ?? '' }}</span>
                                    <span class="badge bg-light text-dark mb-2 text-capitalize">{{ $course->level }}</span>
                                    <h5 class="card-title">{{ $course->title }}</h5>
                                    <p class="text-muted small">by {{ $course->teacher->name ?? 'Instructor' }}</p>
                                    <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-outline-primary">View course</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 d-flex justify-content-center">
                    {{ $courses->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
