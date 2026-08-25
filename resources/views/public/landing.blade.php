@extends('layouts.public')

@section('title', 'AI Quiz — Smart Quizzes from Any Lecture')

@section('content')
    <!-- Hero -->
    <section id="hero" class="hero section">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
                    <h1 data-aos="fade-up">Learn smarter with <span style="color: var(--accent-color);">Agent-powered quizzes</span></h1>
                    <p data-aos="fade-up" data-aos-delay="100">Upload any lecture — PDF, DOCX or pasted notes — and instantly generate adaptive quizzes. Students get personalised feedback on weak areas; teachers save hours of prep time.</p>
                    <div class="d-flex flex-column flex-md-row" data-aos="fade-up" data-aos-delay="200">
                        <a href="{{ route('register') }}" class="btn-get-started">Get Started Free <i class="bi bi-arrow-right"></i></a>
                        <a href="{{ route('courses.index') }}" class="btn-watch-video d-flex align-items-center justify-content-center ms-0 ms-md-4 mt-4 mt-md-0">
                            <i class="bi bi-collection-play"></i><span>Browse Courses</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="zoom-out">
                    <img src="{{ asset('flexstart/img/hero-img.png') }}" class="img-fluid animated" alt="AI quiz hero">
                </div>
            </div>
        </div>
    </section><!-- /Hero -->

    <!-- About -->
    <section id="about" class="about section">
        <div class="container" data-aos="fade-up">
            <div class="row gx-0">
                <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="200">
                    <div class="content">
                        <h3>Why we built this</h3>
                        <h2>Existing platforms force instructors to hand-write every quiz and offer no per-student personalisation.</h2>
                        <p>
                            Coursera, Moodle, Udemy — they're all great at delivery but slow at assessment. Our platform automates quiz creation from any uploaded material using a free LLM, then gives each student a personalised review of their weak areas. Teachers reclaim hours; students learn faster.
                        </p>
                        <div class="text-center text-lg-start">
                            <a href="{{ route('register') }}" class="btn-read-more d-inline-flex align-items-center justify-content-center align-self-center">
                                <span>Start Free</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
                    <img src="{{ asset('flexstart/img/about.jpg') }}" class="img-fluid" alt="About">
                </div>
            </div>
        </div>
    </section><!-- /About -->

    <!-- Values -->
    <section id="values" class="values section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Our Values</h2>
            <p>What makes this platform different<br></p>
        </div>
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card">
                        <img src="{{ asset('flexstart/img/values-1.png') }}" class="img-fluid" alt="">
                        <h3>An Agent that respects context</h3>
                        <p>Questions are generated only from what's in the lecture — no hallucinated facts, no off-topic distractors.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card">
                        <img src="{{ asset('flexstart/img/values-2.png') }}" class="img-fluid" alt="">
                        <h3>Personalised feedback</h3>
                        <p>After every attempt, students get a short paragraph identifying their weak topics and recommending review.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card">
                        <img src="{{ asset('flexstart/img/values-3.png') }}" class="img-fluid" alt="">
                        <h3>Teacher in control</h3>
                        <p>Generated questions are always reviewable and editable before they reach students. The Agent assists, never overrides.</p>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /Values -->

    <!-- Stats -->
    <section id="stats" class="stats section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stats-item d-flex align-items-center w-100 h-100">
                        <i class="bi bi-mortarboard-fill color-blue flex-shrink-0"></i>
                        <div>
                            <span data-purecounter-start="0" data-purecounter-end="{{ max($stats['students'], 1) }}" data-purecounter-duration="1" class="purecounter"></span>
                            <p>Students Enrolled</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-item d-flex align-items-center w-100 h-100">
                        <i class="bi bi-person-workspace color-orange flex-shrink-0" style="color: #ee6c20;"></i>
                        <div>
                            <span data-purecounter-start="0" data-purecounter-end="{{ max($stats['teachers'], 1) }}" data-purecounter-duration="1" class="purecounter"></span>
                            <p>Active Teachers</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-item d-flex align-items-center w-100 h-100">
                        <i class="bi bi-collection-play color-green flex-shrink-0" style="color: #15be56;"></i>
                        <div>
                            <span data-purecounter-start="0" data-purecounter-end="{{ max($stats['courses'], 1) }}" data-purecounter-duration="1" class="purecounter"></span>
                            <p>Published Courses</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stats-item d-flex align-items-center w-100 h-100">
                        <i class="bi bi-patch-question color-pink flex-shrink-0" style="color: #bb0852;"></i>
                        <div>
                            <span data-purecounter-start="0" data-purecounter-end="{{ max($stats['quizzes'], 1) }}" data-purecounter-duration="1" class="purecounter"></span>
                            <p>Agent-Generated Quizzes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /Stats -->

    <!-- Features -->
    <section id="features" class="features section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Features</h2>
            <p>Built for teachers and students<br></p>
        </div>
        <div class="container">
            <div class="row gy-5">
                <div class="col-xl-6" data-aos="zoom-out" data-aos-delay="100">
                    <img src="{{ asset('flexstart/img/features.png') }}" class="img-fluid" alt="">
                </div>
                <div class="col-xl-6 d-flex">
                    <div class="row align-self-center gy-4">
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
                            <div class="feature-box d-flex align-items-center">
                                <i class="bi bi-check"></i>
                                <h3>One-click quiz generation</h3>
                            </div>
                        </div>
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="300">
                            <div class="feature-box d-flex align-items-center">
                                <i class="bi bi-check"></i>
                                <h3>PDF, DOCX &amp; text uploads</h3>
                            </div>
                        </div>
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="400">
                            <div class="feature-box d-flex align-items-center">
                                <i class="bi bi-check"></i>
                                <h3>Easy, medium &amp; hard difficulty</h3>
                            </div>
                        </div>
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
                            <div class="feature-box d-flex align-items-center">
                                <i class="bi bi-check"></i>
                                <h3>Server-timed attempts</h3>
                            </div>
                        </div>
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="600">
                            <div class="feature-box d-flex align-items-center">
                                <i class="bi bi-check"></i>
                                <h3>Per-student weak-area report</h3>
                            </div>
                        </div>
                        <div class="col-md-6" data-aos="fade-up" data-aos-delay="700">
                            <div class="feature-box d-flex align-items-center">
                                <i class="bi bi-check"></i>
                                <h3>Real-time admin dashboard</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /Features -->

    <!-- Services / Categories -->
    <section id="services" class="services section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Course Categories</h2>
            <p>Pick a discipline and start learning<br></p>
        </div>
        <div class="container">
            <div class="row gy-4">
                @php $palette = ['item-cyan','item-orange','item-teal','item-red','item-indigo','item-pink']; @endphp
                @foreach($categories as $i => $cat)
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ 100 + $i * 100 }}">
                        <div class="service-item {{ $palette[$i % count($palette)] }} position-relative">
                            <i class="bi {{ $cat->icon ?? 'bi-book' }} icon"></i>
                            <h3>{{ $cat->name }}</h3>
                            <p>{{ $cat->description }}</p>
                            <a href="{{ route('categories.show', $cat) }}" class="read-more stretched-link"><span>Browse</span> <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section><!-- /Services -->

    <!-- Featured Courses -->
    <section id="portfolio" class="portfolio section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Featured Courses</h2>
            <p>Hand-picked by the platform team</p>
        </div>
        <div class="container">
            @if($featuredCourses->isEmpty())
                <p class="text-center text-muted">No featured courses yet. <a href="{{ route('register') }}">Become a teacher</a> and publish the first one.</p>
            @else
                <div class="row gy-4">
                    @foreach($featuredCourses as $course)
                        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                            <div class="portfolio-content h-100">
                                <img src="{{ $course->thumbnail ? asset('storage/'.$course->thumbnail) : asset('flexstart/img/portfolio/app-1.jpg') }}" class="img-fluid" alt="">
                                <div class="portfolio-info">
                                    <h4>{{ $course->title }}</h4>
                                    <p>by {{ $course->teacher->name ?? 'Instructor' }} &middot; {{ $course->category->name ?? '' }}</p>
                                    <a href="{{ route('courses.show', $course) }}" class="details-link" title="View course"><i class="bi bi-link-45deg"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="text-center mt-5">
                <a href="{{ route('courses.index') }}" class="btn-read-more d-inline-flex align-items-center"><span>All Courses</span><i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </section><!-- /Featured Courses -->

    <!-- Pricing -->
    <section id="pricing" class="pricing section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Pricing</h2>
            <p>Free for students &mdash; tiers for institutions<br></p>
        </div>
        <div class="container">
            <div class="row gy-4">
                @foreach($plans as $i => $plan)
                    <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="{{ 100 + $i * 100 }}">
                        <div class="pricing-tem">
                            @if($i === 1) <span class="featured">Most Popular</span> @endif
                            <h3 style="color: {{ ['#20c997','#0dcaf0','#fd7e14','#0d6efd'][$i % 4] }};">{{ $plan->name }} Plan</h3>
                            <div class="price"><sup>$</sup>{{ (int) $plan->price }}<span> / mo</span></div>
                            <div class="icon">
                                <i class="bi {{ ['bi-box','bi-send','bi-airplane','bi-rocket'][$i % 4] }}" style="color: {{ ['#20c997','#0dcaf0','#fd7e14','#0d6efd'][$i % 4] }};"></i>
                            </div>
                            <ul>
                                @foreach((array) $plan->features as $f)
                                    <li>{{ $f }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('register') }}" class="btn-buy">Get Started</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section><!-- /Pricing -->

    <!-- FAQ teaser -->
    <section id="faq" class="faq section">
        <div class="container section-title" data-aos="fade-up">
            <h2>F.A.Q</h2>
            <p>Common questions</p>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="faq-container">
                        <div class="faq-item faq-active">
                            <h3>How does the Agent generate quizzes?</h3>
                            <div class="faq-content">
                                <p>When a teacher uploads a lecture (PDF / DOCX / pasted text), the platform extracts the text and sends it to the Agent with a strict JSON schema. The Agent returns N multiple-choice questions, each grounded in the source. Teachers can edit or regenerate before publishing.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div>
                        <div class="faq-item">
                            <h3>Is it really free?</h3>
                            <div class="faq-content">
                                <p>Yes — students enrol for free. The Agent runs on free-tier APIs (Gemini primary, Groq fallback), so cost stays near zero. Paid tiers exist for institutions wanting bulk teacher onboarding and priority queue.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div>
                        <div class="faq-item">
                            <h3>What languages are supported?</h3>
                            <div class="faq-content">
                                <p>English is the default. Lectures in Urdu or mixed Urdu/English can also produce reasonable quizzes, since the underlying LLM is multilingual. We officially target English for v1.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="faq-container">
                        <div class="faq-item">
                            <h3>How are teachers verified?</h3>
                            <div class="faq-content">
                                <p>Every teacher signup is reviewed and approved by an admin from the Filament dashboard. Until approved, teacher accounts cannot publish courses or generate quizzes.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div>
                        <div class="faq-item">
                            <h3>What's the personalised feedback?</h3>
                            <div class="faq-content">
                                <p>After every quiz submission, the LLM is given your wrong answers + the correct ones and produces a short paragraph that names your weakest topics and suggests what to review next. It's stored on your attempt and visible from the results page.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div>
                        <div class="faq-item">
                            <h3>Can I use it on mobile?</h3>
                            <div class="faq-content">
                                <p>Yes — the entire platform is mobile-responsive. Browsing courses, taking quizzes and even the teacher / admin dashboards all work on a phone.</p>
                            </div>
                            <i class="faq-toggle bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /FAQ -->

    <!-- Team / Instructors -->
    @if($topInstructors->isNotEmpty())
        <section id="team" class="team section">
            <div class="container section-title" data-aos="fade-up">
                <h2>Top Instructors</h2>
                <p>Learn from the best</p>
            </div>
            <div class="container">
                <div class="row gy-4">
                    @foreach($topInstructors as $i => $t)
                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="{{ 100 + $i * 100 }}">
                            <div class="team-member">
                                <div class="member-img">
                                    <img src="{{ $t->avatar_path ? asset('storage/'.$t->avatar_path) : asset('flexstart/img/team/team-'.(($i % 4)+1).'.jpg') }}" class="img-fluid" alt="">
                                </div>
                                <div class="member-info">
                                    <h4>{{ $t->name }}</h4>
                                    <span>{{ $t->courses_count }} {{ Str::plural('course', $t->courses_count) }}</span>
                                    <p>{{ \Illuminate\Support\Str::limit($t->bio, 120) ?: 'Passionate instructor on the AI Quiz platform.' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Call to action / Contact teaser -->
    <section class="cta section">
        <div class="container">
            <div class="text-center" data-aos="zoom-in">
                <h3 class="mb-3">Ready to try Agent-powered quizzes?</h3>
                <p class="mb-4">Sign up free as a student or apply as a teacher — it takes under a minute.</p>
                <a href="{{ route('register') }}" class="btn-get-started">Create an Account</a>
                <a href="{{ route('contact') }}" class="btn-watch-video d-inline-flex align-items-center justify-content-center ms-md-3"><i class="bi bi-envelope"></i><span>Contact Sales</span></a>
            </div>
        </div>
    </section>
@endsection
