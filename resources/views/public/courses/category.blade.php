@extends('layouts.public')

@section('title', $category->name . ' Courses')

@section('content')
    <section class="hero section">
        <div class="container" data-aos="fade-up">
            <small class="text-uppercase text-muted">Category</small>
            <h1>{{ $category->name }}</h1>
            <p>{{ $category->description }}</p>
        </div>
    </section>

    <section class="services section">
        <div class="container">
            @if($courses->isEmpty())
                <p class="text-center text-muted py-5">No courses published in {{ $category->name }} yet.</p>
            @else
                <div class="row gy-4">
                    @foreach($courses as $course)
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100">
                                <img src="{{ $course->thumbnail ? asset('storage/'.$course->thumbnail) : asset('flexstart/img/portfolio/app-'.(($loop->index % 3)+1).'.jpg') }}" class="card-img-top" alt="">
                                <div class="card-body">
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
