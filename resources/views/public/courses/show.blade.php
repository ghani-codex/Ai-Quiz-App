@extends('layouts.public')

@section('title', $course->title)

@section('content')
    <section class="hero section">
        <div class="container" data-aos="fade-up">
            <small class="text-uppercase text-muted">{{ $course->category->name ?? '' }}</small>
            <h1>{{ $course->title }}</h1>
            <p class="lead">By {{ $course->teacher->name ?? 'Instructor' }} &middot; <span class="text-capitalize">{{ $course->level }}</span></p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-8">
                    <img src="{{ $course->thumbnail ? asset('storage/'.$course->thumbnail) : asset('flexstart/img/about.jpg') }}" class="img-fluid mb-4 rounded" alt="">

                    <h3>About this course</h3>
                    <p>{{ $course->description }}</p>

                    <h3 class="mt-5">Lectures &amp; Quizzes</h3>
                    @if($course->lectures->isEmpty())
                        <p class="text-muted">No lectures yet.</p>
                    @else
                        <div class="accordion" id="lectures-accordion">
                            @foreach($course->lectures as $lec)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#lec{{ $lec->id }}">
                                            <strong>Lecture {{ $loop->iteration }}:</strong> &nbsp;{{ $lec->title }}
                                        </button>
                                    </h2>
                                    <div id="lec{{ $lec->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#lectures-accordion">
                                        <div class="accordion-body">
                                            <p class="text-muted small">Type: {{ $lec->content_type }}</p>
                                            @if($lec->quizzes->where('is_published', true)->count())
                                                <ul class="mb-0">
                                                    @foreach($lec->quizzes->where('is_published', true) as $q)
                                                        <li>
                                                            <i class="bi bi-patch-question text-primary"></i>
                                                            {{ $q->title }}
                                                            <small class="text-muted">({{ $q->question_count }} questions &middot; {{ $q->time_limit_minutes }} min &middot; {{ $q->difficulty }})</small>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-muted small mb-0">No published quiz yet.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <aside class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Course info</h5>
                            <ul class="list-unstyled">
                                <li><strong>Category:</strong> {{ $course->category->name ?? '—' }}</li>
                                <li><strong>Level:</strong> <span class="text-capitalize">{{ $course->level }}</span></li>
                                <li><strong>Lectures:</strong> {{ $course->lectures->count() }}</li>
                                <li><strong>Quizzes:</strong> {{ $course->lectures->sum(fn($l) => $l->quizzes->count()) }}</li>
                                <li><strong>Students enrolled:</strong> {{ $course->enrollments()->count() }}</li>
                            </ul>
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn btn-primary w-100">Enroll in Dashboard</a>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-primary w-100">Sign up to enroll</a>
                            @endauth
                        </div>
                    </div>

                    @if($relatedCourses->isNotEmpty())
                        <div class="mt-4">
                            <h6>Related courses</h6>
                            <ul class="list-unstyled">
                                @foreach($relatedCourses as $r)
                                    <li class="mb-2"><a href="{{ route('courses.show', $r) }}">{{ $r->title }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection
